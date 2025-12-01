jQuery(function ($) {
  "use strict";

  // --- متغیرهای اصلی DOM ---
  const $body = $("body");
  const $form = $('#authForm');
  const $submitBtn = $('#submit-btn');
  const $mobileInput = $('#mobile');
  const $otpInput = $('#otp');
  const $stepMobile = $('#step-mobile');
  const $stepOtp = $('#step-otp');
  const $subtitle = $('#form-subtitle');
  
  // متغیر وضعیت
  let currentStep = 'mobile';

  // ===========================================================================
  // 1. UTILITIES (توابع کمکی اعداد و ورودی‌ها)
  // ===========================================================================

  // تبدیل اعداد فارسی/عربی به انگلیسی
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
            e.key === "Home" || e.key === "End" || e.ctrlKey || e.metaKey);
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
    pastedData = pastedData.replace(/\D/g, ""); // فقط اعداد
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
  // 2. INPUT CONFIGURATION (اعمال تنظیمات روی فیلدها)
  // ===========================================================================

  setupNumericField("#mobile", { maxLength: 11 });
  setupNumericField("#otp", { maxLength: 5 });

  // ===========================================================================
  // 3. MAIN FORM LOGIC (منطق ارسال و دریافت)
  // ===========================================================================

  // رویداد ارسال فرم
  $form.on("submit", function (e) {
    e.preventDefault();

    // چک باکس قوانین (اگر وجود دارد)
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

  // دکمه بازگشت / ویرایش شماره
  $('#edit-mobile-btn').on('click', function() {
    currentStep = 'mobile';
    $stepOtp.hide();
    $stepMobile.fadeIn();
    $mobileInput.prop('disabled', false).focus();
    $submitBtn.text('ارسال کد تایید');
    $subtitle.text('شماره موبایل خود را وارد نمایید');
  });

  // دکمه ارسال مجدد کد
  $('#resend-otp-btn').on('click', function() {
    handleSendOtp(true);
  });

  // ---------------------------------------------------------------------------
  // STEP 1: تابع ارسال کد تایید
  // ---------------------------------------------------------------------------
  function handleSendOtp(isResend = false) {
    const phone = convertToEnglishDigits($mobileInput.val().trim());

    if (phone === "" || phone.length < 11 || !phone.startsWith('09')) {
      Swal.fire({ icon: "warning", title: "خطا!", text: "شماره موبایل معتبر نیست!", confirmButtonText: "باشه" });
      return;
    }

    const originalText = isResend ? 'ارسال مجدد کد' : 'ارسال کد تایید';
    const loadingText = isResend ? 'در حال ارسال...' : 'در حال پردازش...';
    const $targetBtn = isResend ? $('#resend-otp-btn') : $submitBtn;

    // فعال سازی لودینگ
    $targetBtn.text(loadingText).prop('disabled', true).css("opacity", "0.7");

    // فراخوانی wbsAjax
    wbsAjax(
        'akamode_send_otp',       // نام اکشن در PHP
        { mobile: phone },        // داده‌ها
        'json',                   // فرمت پاسخ
        
        // callback موفقیت (زمانی که سرور پاسخ دهد)
        function(res) {
            if (res.success) {
                if (!isResend) {
                    goToStepTwo(phone);
                } else {
                    Swal.fire({
                        icon: "success", title: "ارسال شد", text: "کد تایید ارسال شد.", 
                        timer: 2000, showConfirmButton: false
                    });
                }
            } else {
                // اگر سرور پاسخ داد ولی عملیات ناموفق بود (مثلا شماره مسدود است)
                Swal.fire({ icon: "error", title: "خطا!", text: res.data.message || "خطایی رخ داد.", confirmButtonText: "باشه" });
            }
        },
        
        // callback خطا (زمانی که ارتباط شبکه قطع است یا ارور 500)
        function(error) {
             Swal.fire({ icon: "error", title: "خطا!", text: "خطا در ارتباط با سرور.", confirmButtonText: "باشه" });
        },

        // callback نهایی (همیشه اجرا می‌شود)
        function() {
            // اگر عملیات موفق نبود یا ارسال مجدد بود، دکمه را آزاد کن
            // (اگر به مرحله ۲ رفته باشیم، دکمه تغییر کرده و نیازی به این کار نیست)
            if(isResend || currentStep === 'mobile') {
                $targetBtn.prop('disabled', false).css("opacity", "1").text(originalText);
            }
        }
    );
  }

  // تابع کمکی برای رفتن به مرحله ۲
  function goToStepTwo(phone) {
    currentStep = 'otp';
    $stepMobile.hide();
    $mobileInput.prop('disabled', true);
    
    $stepOtp.fadeIn();
    $otpInput.val('').focus();
    
    $submitBtn.text('ورود').prop('disabled', false).css("opacity", "1"); // ریست دکمه اصلی
    
    // نمایش شماره در متن راهنما
    $subtitle.html(`کد ارسالی به شماره <span dir="ltr" style="font-weight:bold">${phone}</span> را وارد نمایید`);
  }

  // ---------------------------------------------------------------------------
  // STEP 2: تابع بررسی کد و ورود
  // ---------------------------------------------------------------------------
  function handleVerifyOtp() {
    const phone = convertToEnglishDigits($mobileInput.val().trim());
    const code = convertToEnglishDigits($otpInput.val().trim());

    if (code === "" || code.length < 4) {
      Swal.fire({ icon: "warning", title: "خطا!", text: "لطفا کد تایید را کامل وارد کنید.", confirmButtonText: "باشه" });
      return;
    }

    $submitBtn.text('در حال ورود...').prop('disabled', true).css("opacity", "0.7");

    wbsAjax(
        'akamode_verify_otp',
        { mobile: phone, otp: code },
        'json',
        
        // callback موفقیت
        function(res) {
            if (res.success) {
                // ورود موفق
                Swal.fire({ icon: "success", title: "خوش آمدید!", text: res.data.message, showConfirmButton: false, timer: 1500 });
                setTimeout(function() {
                    window.location.href = res.data.redirect_url;
                }, 1500);
            } else {
                // کد اشتباه است (پیام دقیق از PHP می‌آید)
                Swal.fire({ icon: "error", title: "خطا!", text: res.data.message, confirmButtonText: "تلاش مجدد" });
                $submitBtn.prop('disabled', false).css("opacity", "1").text('ورود');
            }
        },

        // callback خطای شبکه
        function(error) {
            Swal.fire({ icon: "error", title: "خطا!", text: "خطا در ارتباط با سرور.", confirmButtonText: "باشه" });
            $submitBtn.prop('disabled', false).css("opacity", "1").text('ورود');
        },

        // callback نهایی
        function() {
            // کار خاصی نیاز نیست
        }
    );
  }

});