$(function () {

    /* ── Mobile nav toggle ───────────────────────── */
    $('#navToggle').on('click', function () {
        $('#mainNav').toggleClass('open');
    });
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.site-header').length) {
            $('#mainNav').removeClass('open');
        }
    });

    /* ── Admin tab switcher ──────────────────────── */
    $('.tab-btn').on('click', function () {
        var tab = $(this).data('tab');
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').addClass('hidden');
        $('#tab-' + tab).removeClass('hidden');
    });

    /* ── Register form: live validation ─────────── */
    $('#registerForm').on('submit', function (e) {
        var ok = true;
        $(this).find('input[required]').each(function () {
            var $err = $(this).siblings('.field-error');
            if (!this.value.trim()) {
                $err.text('This field is required.');
                $(this).addClass('is-invalid');
                ok = false;
            } else {
                $err.text('');
                $(this).removeClass('is-invalid');
            }
        });
        var pw  = $('#password').val();
        var cpw = $('#confirm').val();
        if (pw && cpw && pw !== cpw) {
            $('#confirm').addClass('is-invalid').siblings('.field-error').text('Passwords do not match.');
            ok = false;
        }
        if (!ok) e.preventDefault();
    });

    /* ── Clear invalid state on input ───────────── */
    $('input').on('input', function () {
        if ($(this).hasClass('is-invalid') && this.value.trim()) {
            $(this).removeClass('is-invalid').siblings('.field-error').text('');
        }
    });

    /* ── Color picker → HEX / RGBA / HSL ────────── */
    function hexToRgb(hex) {
        return {
            r: parseInt(hex.slice(1, 3), 16),
            g: parseInt(hex.slice(3, 5), 16),
            b: parseInt(hex.slice(5, 7), 16)
        };
    }

    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        var max = Math.max(r, g, b), min = Math.min(r, g, b),
            h, s, l = (max + min) / 2;
        if (max === min) {
            h = s = 0;
        } else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                case g: h = ((b - r) / d + 2) / 6; break;
                case b: h = ((r - g) / d + 4) / 6; break;
            }
        }
        return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
    }

    function updateColor(hex) {
        var rgb = hexToRgb(hex);
        var hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
        $('#hexVal').text(hex.toUpperCase());
        $('#rgbaVal').text('rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',1)');
        $('#hslVal').text('hsl(' + hsl.h + ',' + hsl.s + '%,' + hsl.l + '%)');
        $('#colorPreview').css('background', hex)
            .css('box-shadow', '0 4px 20px rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',.4)');
    }

    $('#colorPicker').on('input change', function () { updateColor($(this).val()); });
    if ($('#colorPicker').length) updateColor($('#colorPicker').val());

    /* ── Copy badge value to clipboard ───────────── */
    $('.color-values').on('click', '.badge', function () {
        var $b  = $(this);
        var val = $b.text();
        if (navigator.clipboard) {
            navigator.clipboard.writeText(val).then(function () {
                $b.addClass('copied').text('Copied!');
                setTimeout(function () {
                    $b.removeClass('copied').text(val);
                }, 1200);
            });
        }
    });

    /* ── Upload progress bar (simulated) ─────────── */
    $('#uploadForm').on('submit', function () {
        if (!$('#photo').val()) return;
        $('#progressWrap').show();
        var $bar = $('#progressBar'), pct = 0;
        var iv = setInterval(function () {
            pct += Math.random() * 12;
            if (pct > 88) pct = 88;
            $bar.css('width', pct + '%');
        }, 120);
        $(this).data('iv', iv);
    });

    /* ── Photo card hover: lift author into view ─── */
    $('#masonryGrid').on('mouseenter', '.photo-card', function () {
        $(this).stop(true).animate({ 'margin-top': '-2px' }, 120);
    }).on('mouseleave', '.photo-card', function () {
        $(this).stop(true).animate({ 'margin-top': '0px' }, 120);
    });

    /* ── Smooth scroll to #gallery ───────────────── */
    $('a[href="#gallery"]').on('click', function (e) {
        var $t = $('#gallery');
        if ($t.length) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: $t.offset().top - 70 }, 380);
        }
    });

    /* ── Tag pills: active state feedback ────────── */
    $('.tag-pill').on('click', function () {
        $('.tag-pill').removeClass('active');
        $(this).addClass('active');
    });

    /* ── Alert auto-dismiss after 5 s ────────────── */
    setTimeout(function () {
        $('.alert').fadeOut(400);
    }, 5000);

});
