<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Laravel 12's configureSmtpTransport() does not read the 'stream' key from
        // config/mail.php, so we must override the smtp transport creator entirely.
        // This disables SSL peer verification so Gmail SMTP works inside Docker
        // where the container's CA bundle is not trusted by PHP's OpenSSL.
        Mail::extend('smtp', function (array $config) {
            $factory = new EsmtpTransportFactory();

            $scheme = $config['scheme'] ?? null;
            if (! $scheme) {
                $scheme = ($config['port'] == 465) ? 'smtps' : 'smtp';
            }

            $transport = $factory->create(new Dsn(
                $scheme,
                $config['host'],
                $config['username'] ?? null,
                $config['password'] ?? null,
                $config['port'] ?? null,
                $config
            ));

            if (! $transport instanceof EsmtpTransport) {
                return $transport;
            }

            $stream = $transport->getStream();
            if ($stream instanceof SocketStream) {
                $stream->setStreamOptions([
                    'ssl' => [
                        'verify_peer'      => false,
                        'verify_peer_name' => false,
                    ],
                ]);
            }

            return $transport;
        });
    }
}
