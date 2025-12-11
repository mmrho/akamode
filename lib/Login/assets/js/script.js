jQuery(function ($) {
  "use strict";

  // --- Core DOM variables ---
  const $body = $("body");
  const $form = $('#authForm');
  const $submitBtn = $('#submit-btn');
  const $mobileInput = $('#mobile');
  const $otpInput = $('#otp');
  const $stepMobile = $('#step-mobile');
  const $stepOtp = $('#step-otp');
  const $subtitle = $('#form-subtitle');
  
  // متغیرهای مربوط به تایمر
  const $resendBtn = $('#resend-otp-btn');
  const $otpTimer = $('#otp-timer');
  let countdownInterval;
  
  let currentStep = 'mobile';

  // ===========================================================================
  // 1. UTILITIES 
  // ===========================================================================

  function convertToEnglishDigits(str) {
    if (!str) return str;
    const digitMappings = {
      "٠": "0", "١": "1", "٢": "2", "٣": "3", "٤": "4", "٥": "5", "٦": "6", "٧": "7", "٨": "8", "٩": "9",
      "۰": "0", "۱": "1", "۲": "2", "۳": "3", "۴": "4", "۵": "5", "۶": "6", "۷": "7", "۸": "8", "۹": "9"
    };
    let result = str.toString();
    for (let digit in digitMappings) {
      result = result.replace(new RegExp(digit, "g"), digitMappings[digit]);
    }
    return result;
  }

  const persianDigits = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];
  const englishDigits = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
  const arabicDigits = ["٠", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩"];

  function isSpecialKey(e) {
    return (e.key === "Backspace" || e.key === "Delete" || e.key === "Tab" || 
            e.key === "ArrowLeft" || e.key === "ArrowRight" || 
            e.key === "Home" || e.key === "End" || e.key === "Enter" || 
            e.ctrlKey || e.metaKey);
  }
  
  function handlePersianDigitKeydown(e) {
    if (persianDigits.includes(e.key) || arabicDigits.includes(e.key)) {
      let englishDigit;
      if (persianDigits.includes(e.key)) {
        englishDigit = englishDigits[persianDigits.indexOf(e.key)];
      } else {
        englishDigit = englishDigits[arabicDigits.indexOf(e.key)];
      }
      const start = this.selectionStart;
      const end = this.selectionEnd;
      const value = this.value;
      this.value = value.substring(0, start) + englishDigit + value.substring(end);
      this.selectionStart = this.selectionEnd = start + 1;
      $(this).trigger("input");
      e.preventDefault();
      return false;
    }
    return true;
  }

  function handleNumericPaste(e, maxLength) {
    let pastedData = e.originalEvent.clipboardData.getData("text");
    pastedData = convertToEnglishDigits(pastedData);
    pastedData = pastedData.replace(/\D/g, ""); 
    pastedData = pastedData.substring(0, maxLength);
    $(this).val(pastedData);
    e.preventDefault();
    $(this).trigger("input");
  }

  function setupNumericField(selector, options) {
    const field = $(selector);
    const defaultOptions = { maxLength: 10, errorMessage: "لطفا فقط عدد وارد کنید" };
    options = { ...defaultOptions, ...options };
    
    field.on("keydown", function (e) {
      if (isSpecialKey(e)) return true;
      if (e.code.startsWith("Numpad") && e.key >= "0" && e.key <= "9") return true;
      if (!handlePersianDigitKeydown.call(this, e)) return false;
      if (englishDigits.includes(e.key)) {
        if (this.value.length >= options.maxLength && this.selectionStart === this.selectionEnd) {
          e.preventDefault();
          return false;
        }
        return true;
      }
      e.preventDefault();
      return false;
    });

    field.on("paste", function (e) {
      handleNumericPaste.call(this, e, options.maxLength);
    });

    return field;
  }

  // ===========================================================================
  // 2. INPUT CONFIGURATION
  // ===========================================================================

  setupNumericField("#mobile", { maxLength: 11 });
  setupNumericField("#otp", { maxLength: 5 });

  // ===========================================================================
  // 3. TIMER LOGIC (بخش اصلاح شده)
  // ===========================================================================
  
  function startOtpTimer(durationInSeconds) {
      // پاک کردن اینتروال قبلی اگر وجود دارد
      if (countdownInterval) clearInterval(countdownInterval);
      
      let timer = durationInSeconds, minutes, seconds;
      
      // وضعیت اولیه: دکمه مخفی، تایمر نمایان
      $resendBtn.hide();
      $otpTimer.show().text("02:00"); 

      countdownInterval = setInterval(function () {
          minutes = parseInt(timer / 60, 10);
          seconds = parseInt(timer % 60, 10);

          minutes = minutes < 10 ? "0" + minutes : minutes;
          seconds = seconds < 10 ? "0" + seconds : seconds;

          $otpTimer.text(minutes + ":" + seconds);

          // وقتی زمان تمام شد
          if (--timer < 0) {
              clearInterval(countdownInterval);
              $otpTimer.hide();       // مخفی کردن تایمر
              
              // [اصلاح باگ]: بازنشانی متن و وضعیت دکمه قبل از نمایش
              $resendBtn.text('ارسال مجدد کد')
                        .prop('disabled', false)
                        .css("opacity", "1");
              
              $resendBtn.fadeIn();    // نمایش دکمه
          }
      }, 1000);
  }

  // ===========================================================================
  // 4. MAIN FORM LOGIC
  // ===========================================================================

  $form.on("submit", function (e) {
    e.preventDefault();

    const agreementCheckbox = $("#agreement-checkbox");
    if (agreementCheckbox.length > 0 && !agreementCheckbox.is(':checked')) {
      Swal.fire({
        icon: "warning", title: "خطا!", text: "لطفا با قوانین موافقت کنید.",
        confirmButtonText: "باشه", target: ".wbs-popup-panel",
      });
      return false;
    }

    if (currentStep === 'mobile') {
      handleSendOtp();
    } else {
      handleVerifyOtp();
    }
  });

  $('#edit-mobile-btn').on('click', function() {
    // ریست کردن تایمر هنگام بازگشت
    if (countdownInterval) clearInterval(countdownInterval);
    
    currentStep = 'mobile';
    $stepOtp.hide();
    $stepMobile.fadeIn();
    $mobileInput.prop('disabled', false).focus();
    $submitBtn.text('ارسال کد تایید');
    $subtitle.text('شماره موبایل خود را وارد نمایید');
  });

  // هندلر کلیک روی دکمه ارسال مجدد
  $('#resend-otp-btn').on('click', function() {
    handleSendOtp(true); // true یعنی این یک ارسال مجدد است
  });

  // ---------------------------------------------------------------------------
  // STEP 1: Send OTP
  // ---------------------------------------------------------------------------
  function handleSendOtp(isResend = false) {
    const phone = convertToEnglishDigits($mobileInput.val().trim());

    if (phone === "" || phone.length < 11 || !phone.startsWith('09')) {
      Swal.fire({ icon: "warning", title: "خطا!", text: "شماره موبایل معتبر نیست!", confirmButtonText: "باشه" });
      return;
    }

    const originalText = isResend ? 'ارسال مجدد کد' : 'ارسال کد تایید';
    const loadingText = isResend ? 'در حال ارسال...' : 'در حال پردازش...';
    
    // انتخاب دکمه مناسب برای نمایش لودینگ
    const $targetBtn = isResend ? $resendBtn : $submitBtn;

    // دکمه به حالت لودینگ می‌رود (غیرفعال می‌شود)
    $targetBtn.text(loadingText).prop('disabled', true).css("opacity", "0.7");

    wbsAjax(
        'akamode_send_otp',
        { mobile: phone },
        'json',
        function(res) {
            if (res.success) {
                // اگر موفق بود، تایمر شروع می‌شود و دکمه را مخفی می‌کند
                // و وقتی تایمر تمام شد، دکمه ریست شده و دوباره نمایش داده می‌شود (در تابع startOtpTimer)
                startOtpTimer(120);

                if (!isResend) {
                    goToStepTwo(phone);
                } else {
                    Swal.fire({
                        icon: "success", title: "ارسال شد", text: "کد تایید مجدداً ارسال شد.", 
                        timer: 2000, showConfirmButton: false
                    });
                }
            } else {
                Swal.fire({ icon: "error", title: "خطا!", text: res.data.message || "خطایی رخ داد.", confirmButtonText: "باشه" });
                
                // اگر خطا داد، باید دکمه ارسال مجدد را همان لحظه برگردانیم تا کاربر دوباره تلاش کند
                if (isResend) {
                     $targetBtn.text("ارسال مجدد کد").prop('disabled', false).css("opacity", "1");
                }
            }
        },
        function(error) {
             Swal.fire({ icon: "error", title: "خطا!", text: "خطا در ارتباط با سرور.", confirmButtonText: "باشه" });
             if (isResend) {
                 $targetBtn.text("ارسال مجدد کد").prop('disabled', false).css("opacity", "1");
             }
        },
        function() {
            // برای دکمه اصلی (مرحله اول)
            if(!isResend && currentStep === 'mobile') {
                $targetBtn.prop('disabled', false).css("opacity", "1").text(originalText);
            }
        }
    );
  }

  function goToStepTwo(phone) {
    currentStep = 'otp';
    $stepMobile.hide();
    $mobileInput.prop('disabled', true);
    
    $stepOtp.fadeIn();
    $otpInput.val('').focus();
    
    $submitBtn.text('ورود').prop('disabled', false).css("opacity", "1");
    
    $subtitle.html(`کد ارسالی به شماره <span dir="ltr" style="font-weight:bold">${phone}</span> را وارد نمایید`);
  }

  // ---------------------------------------------------------------------------
  // STEP 2: Verify OTP
  // ---------------------------------------------------------------------------
  function handleVerifyOtp() {
    const phone = convertToEnglishDigits($mobileInput.val().trim());
    const code = convertToEnglishDigits($otpInput.val().trim());
    const redirectUrl = $('#redirect_to_field').val(); 

    if (code === "" || code.length < 4) {
      Swal.fire({ icon: "warning", title: "خطا!", text: "لطفا کد تایید را کامل وارد کنید.", confirmButtonText: "باشه" });
      return;
    }

    $submitBtn.text('در حال ورود...').prop('disabled', true).css("opacity", "0.7");

    wbsAjax(
        'akamode_verify_otp',
        { 
            mobile: phone, 
            otp: code,
            redirect_to: redirectUrl 
        },
        'json',
        
        function(res) {
            if (res.success) {
                Swal.fire({ icon: "success", title: "خوش آمدید!", text: res.data.message, showConfirmButton: false, timer: 1500 });
                setTimeout(function() {
                    window.location.href = res.data.redirect_url;
                }, 1500);
            } else {
                Swal.fire({ icon: "error", title: "خطا!", text: res.data.message, confirmButtonText: "تلاش مجدد" });
                $submitBtn.prop('disabled', false).css("opacity", "1").text('ورود');
            }
        },
        function(error) {
            Swal.fire({ icon: "error", title: "خطا!", text: "خطا در ارتباط با سرور.", confirmButtonText: "باشه" });
            $submitBtn.prop('disabled', false).css("opacity", "1").text('ورود');
        },
        function() {
        }
    );
  }

});