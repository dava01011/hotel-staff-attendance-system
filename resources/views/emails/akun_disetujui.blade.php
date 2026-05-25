<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Disetujui</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;">

                    {{-- ── Header ── --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#354591 0%,#4a5db8 100%);border-radius:16px 16px 0 0;padding:40px 40px 32px;text-align:center;">

                            {{-- Icon --}}
                            <div style="width:68px;height:68px;background:rgba(255,255,255,0.18);border-radius:50%;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;">
                                <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png"
                                     width="36" height="36"
                                     alt="check"
                                     style="display:block;margin:16px auto 0;">
                            </div>

                            <h1 style="color:white;font-size:22px;font-weight:700;margin:0 0 8px;">
                                Akun Berhasil Diaktifkan!
                            </h1>
                            <p style="color:rgba(255,255,255,0.85);font-size:14px;margin:0;">
                                Selamat bergabung di Sistem Absensi Hotel Harris
                            </p>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td style="background:white;padding:36px 40px;">

                            <p style="font-size:16px;font-weight:600;color:#1e293b;margin:0 0 12px;">
                                Halo, {{ $user->nama }} 👋
                            </p>

                            <p style="font-size:14px;color:#475569;line-height:1.7;margin:0 0 28px;">
                                Akun Anda di <strong>Sistem Absensi Hotel Harris Festival City Link Bandung</strong>
                                telah <strong style="color:#10b981;">disetujui dan diaktifkan</strong> oleh administrator.
                                Anda sekarang dapat masuk dan menggunakan semua fitur yang tersedia.
                            </p>

                            {{-- Status card --}}
                            <table width="100%" cellpadding="0" cellspacing="0"
                                   style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;margin-bottom:28px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="vertical-align:middle;padding-right:14px;">
                                                    <div style="width:42px;height:42px;background:#10b981;border-radius:50%;text-align:center;line-height:42px;">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png"
                                                             width="20" height="20"
                                                             alt="ok"
                                                             style="vertical-align:middle;margin-top:-2px;">
                                                    </div>
                                                </td>
                                                <td style="vertical-align:middle;">
                                                    <div style="font-size:11px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px;">
                                                        Status Akun
                                                    </div>
                                                    <div style="font-size:15px;font-weight:700;color:#065f46;">
                                                        Aktif &amp; Siap Digunakan
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Info box --}}
                            <table width="100%" cellpadding="0" cellspacing="0"
                                   style="background:#f8fafc;border-radius:10px;margin-bottom:28px;">
                                <tr>
                                    <td style="padding:6px 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:13px;color:#64748b;">
                                                    Email Login
                                                </td>
                                                <td style="padding:10px 0;border-bottom:1px solid #f0f0f0;font-size:13px;font-weight:600;color:#1e293b;text-align:right;">
                                                    {{ $user->email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:10px 0;font-size:13px;color:#64748b;">
                                                    Sistem
                                                </td>
                                                <td style="padding:10px 0;font-size:13px;font-weight:600;color:#1e293b;text-align:right;">
                                                    Absensi Hotel Harris
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA Button --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('login') }}"
                                           style="display:inline-block;background:linear-gradient(135deg,#354591 0%,#4a5db8 100%);color:white;text-decoration:none;padding:14px 48px;border-radius:10px;font-size:15px;font-weight:700;letter-spacing:0.3px;">
                                            Masuk Sekarang
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:12px;color:#94a3b8;text-align:center;line-height:1.6;margin:0;">
                                Jika Anda tidak merasa mendaftar di sistem ini,<br>
                                abaikan email ini atau hubungi administrator.
                            </p>

                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="background:#f8fafc;border-top:1px solid #e9ecef;border-radius:0 0 16px 16px;padding:20px 40px;text-align:center;">
                            <p style="font-size:12px;color:#94a3b8;margin:0;line-height:1.6;">
                                Email ini dikirim otomatis oleh <strong style="color:#354591;">Sistem Absensi Hotel Harris</strong>.<br>
                                Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
