$(function () {

    /* ── Mobile nav toggle ─────────────────────── */
    $('.nav-toggle').on('click', function () {
        $('.main-nav').toggleClass('open');
    });

    /* ── Admin tab switcher ─────────────────────── */
    $('.tab-btn').on('click', function () {
        var target = $(this).data('tab');
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').addClass('hidden');
        $('#tab-' + target).removeClass('hidden');
    });

    /* ── Register form live validation ────────────── */
    $('#registerForm').on('submit', function (e) {
        var valid = true;
        $(this).find('input[required]').each(function () {
            var err = $(this).siblings('.field-error');
            if (!this.value.trim()) {
                err.text('This field is required.');
                $(this).addClass('is-invalid');
                valid = false;
            } else {
                err.text('');
                $(this).removeClass('is-invalid');
            }
        });
        var pw  = $('#password').val();
        var cpw = $('#confirm').val();
        if (pw && cpw && pw !== cpw) {
            $('#confirm').addClass('is-invalid').siblings('.field-error').text('Passwords do not match.');
            valid = false;
        }
        if (!valid) e.preventDefault();
    });

    /* ── Color wheel → HEX / RGBA / HSL display ────── */
    function hexToRgb(hex) {
        var r = parseInt(hex.slice(1,3),16);
        var g = parseInt(hex.slice(3,5),16);
        var b = parseInt(hex.slice(5,7),16);
        return {r:r, g:g, b:b};
    }
    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        var max = Math.max(r,g,b), min = Math.min(r,g,b), h, s, l = (max+min)/2;
        if (max === min) { h = s = 0; }
        else {
            var d = max - min;
            s = l > 0.5 ? d/(2-max-min) : d/(max+min);
            switch(max){
                case r: h = ((g-b)/d + (g<b?6:0))/6; break;
                case g: h = ((b-r)/d + 2)/6; break;
                case b: h = ((r-g)/d + 4)/6; break;
            }
        }
        return { h: Math.round(h*360), s: Math.round(s*100), l: Math.round(l*100) };
    }

    function updateColor(hex) {
        var rgb = hexToRgb(hex);
        var hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
        $('#hexVal').text(hex.toUpperCase());
        $('#rgbaVal').text('rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',1)');
        $('#hslVal').text('hsl(' + hsl.h + ',' + hsl.s + '%,' + hsl.l + '%)');
        $('#colorPreview').css('background', hex);
    }

    $('#colorPicker').on('input change', function () {
        updateColor($(this).val());
    });
    if ($('#colorPicker').length) updateColor($('#colorPicker').val());

    /* ── Copy color value on badge click ────────── */
    $('.color-values').on('click', '.badge', function () {
        var val = $(this).text();
        navigator.clipboard.writeText(val).then(function () {
            var orig = $(this).text();
        }.bind(this));
        $(this).animate({ opacity: 0.4 }, 150).animate({ opacity: 1 }, 150);
    });

    /* ── File upload progress bar (simulated) ───── */
    $('#uploadForm').on('submit', function () {
        var $wrap = $('#progressWrap').show();
        var $bar  = $('#progressBar');
        var pct   = 0;
        var iv = setInterval(function () {
            pct += Math.random() * 15;
            if (pct > 90) pct = 90;
            $bar.css('width', pct + '%');
        }, 150);
        $(this).data('interval', iv);
    });

    /* ── Smooth scroll for anchor links ────────── */
    $('a[href^="#"]').on('click', function (e) {
        var target = $($(this).attr('href'));
        if (target.length) {
            e.preventDefault();
            $('html,body').animate({ scrollTop: target.offset().top - 70 }, 400);
        }
    });

    /* ── Card hover pulse via jQuery ──────────── */
    $('.card').on('mouseenter', function () {
        $(this).find('.card-icon').stop(true).animate({ fontSize: '3rem' }, 200);
    }).on('mouseleave', function () {
        $(this).find('.card-icon').stop(true).animate({ fontSize: '2.5rem' }, 200);
    });

});
