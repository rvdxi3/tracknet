<?php

namespace App\Services;

class TotpService
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const STEP = 30;      // 30-second window
    private const DIGITS = 6;     // 6-digit code
    private const TOLERANCE = 1;  // ±1 window for clock drift

    /**
     * Generate a new Base32-encoded TOTP secret (20 bytes = 160 bits).
     */
    public function generateSecret(): string
    {
        $bytes = random_bytes(20);
        return $this->base32Encode($bytes);
    }

    /**
     * Return a QR code image URL for the user to scan with an authenticator app.
     */
    public function getQrUrl(string $secret, string $email): string
    {
        $label   = rawurlencode('TrackNet:' . $email);
        $issuer  = rawurlencode('TrackNet');
        $otpauth = "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}&digits=6&period=30";

        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . rawurlencode($otpauth);
    }

    /**
     * Verify a 6-digit TOTP code.  Accepts ±TOLERANCE windows for clock drift.
     */
    public function verify(string $secret, string $code): bool
    {
        $secretBytes = $this->base32Decode($secret);
        $timestamp   = (int) floor(time() / self::STEP);

        for ($offset = -self::TOLERANCE; $offset <= self::TOLERANCE; $offset++) {
            if ($this->hotp($secretBytes, $timestamp + $offset) === $code) {
                return true;
            }
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * HOTP algorithm (RFC 4226): HMAC-SHA1 counter-based OTP.
     */
    private function hotp(string $secretBytes, int $counter): string
    {
        // Pack counter as 8-byte big-endian
        $message = pack('J', $counter);

        $hash    = hash_hmac('sha1', $message, $secretBytes, true);
        $offset  = ord($hash[19]) & 0x0F;

        $code = (
            ((ord($hash[$offset])     & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) <<  8) |
            ((ord($hash[$offset + 3]) & 0xFF))
        ) % (10 ** self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    /**
     * Encode binary string to Base32 (RFC 4648).
     */
    private function base32Encode(string $data): string
    {
        $chars   = self::BASE32_CHARS;
        $output  = '';
        $len     = strlen($data);
        $buffer  = 0;
        $bitsLeft = 0;

        for ($i = 0; $i < $len; $i++) {
            $buffer    = ($buffer << 8) | ord($data[$i]);
            $bitsLeft += 8;

            while ($bitsLeft >= 5) {
                $bitsLeft -= 5;
                $output   .= $chars[($buffer >> $bitsLeft) & 0x1F];
            }
        }

        if ($bitsLeft > 0) {
            $output .= $chars[($buffer << (5 - $bitsLeft)) & 0x1F];
        }

        return $output;
    }

    /**
     * Decode Base32 string to binary (RFC 4648, case-insensitive).
     */
    private function base32Decode(string $data): string
    {
        $data     = strtoupper($data);
        $chars    = self::BASE32_CHARS;
        $output   = '';
        $buffer   = 0;
        $bitsLeft = 0;

        for ($i = 0; $i < strlen($data); $i++) {
            $ch  = $data[$i];
            $pos = strpos($chars, $ch);

            if ($pos === false) {
                continue; // skip padding / unknown chars
            }

            $buffer    = ($buffer << 5) | $pos;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output   .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }
}
