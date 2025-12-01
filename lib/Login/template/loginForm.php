<div class="loginContainer">
    <div class="login-main-container">
        <div class="background-container">
            <img src="<?php echo get_template_directory_uri(); ?>/images/temp/akamode-login.jpg" alt="akamode-login-img">
        </div>
        <div class="main-wrapper">
            <section class="form-panel">
                <a href="<?php echo home_url(); ?>" class="back-link">
                    <i class="icon-up-left-arrow"></i>
                    <span> بازگشت به سایت</span>
                </a>
                <div class="content-wrapper">
                    <div class="mobile-logo">
                        <div class="logo-icon">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/SVG/akamode-logo.svg" alt="akamode-logo">
                        </div>
                    </div>
                    <div class="login-container">
                        <h2>ورود | ثبت نام</h2>
                        
                        <p class="subtitle" id="form-subtitle">شماره موبایل خود را وارد نمایید</p>
                        
                        <form id="authForm" class="login-form" method="post" autocomplete="off">
                            
                            <div class="form-group" id="step-mobile">
                                <input dir="ltr" class="input" type="text" inputmode="numeric" maxlength="11" id="mobile" name="mobile" placeholder=" " required />
                                <label for="mobile" class="form-label">شماره موبایل</label>
                            </div>

                            <div class="form-group" id="step-otp" style="display: none;">
                                <input dir="ltr" class="input otp-input" type="text" inputmode="numeric" maxlength="5" id="otp" name="otp" placeholder=" " />
                                <div class="otp-actions">
                                    <button type="button" id="edit-mobile-btn">تغییر شماره</button>
                                    <button type="button" id="resend-otp-btn">ارسال مجدد کد</button>
                                </div>
                            </div>

                            <button type="submit" class="login-btn" id="submit-btn">ورود</button>
                        </form>
                        
                        <div id="msg-box" style="margin-top: 15px; font-size: 12px; min-height: 20px;"></div>

                    </div>
                </div>
                <a href="#" class="help-link">
                    <span>مشکل دارید؟</span>
                    <i class="icon-up-left-arrow"></i>
                </a>
            </section>
            
            <section class="image-panel">
                <div class="logo-box">
                    <div class="logo-icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/images/SVG/akamode-logo.svg" alt="akamode-logo">
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>