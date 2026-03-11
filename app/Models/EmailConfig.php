<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class EmailConfig extends Model
{
    protected $table = 'email_configs';

    protected $fillable = [
        'name',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'from_email',
        'from_name',
        'encryption',
        'is_active',
    ];

    protected $hidden = [
        'smtp_password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'smtp_port' => 'integer',
    ];

    /**
     * Get the decrypted SMTP password.
     */
    public function getSmtpPasswordDecryptedAttribute(): ?string
    {
        if (empty($this->attributes['smtp_password'])) {
            return null;
        }
        try {
            return Crypt::decryptString($this->attributes['smtp_password']);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Set the SMTP password (encrypt before save).
     */
    public function setSmtpPasswordAttribute(?string $value): void
    {
        $this->attributes['smtp_password'] = $value
            ? Crypt::encryptString($value)
            : null;
    }

    /**
     * Get decrypted password for use when configuring mail.
     */
    public function getDecryptedPassword(): ?string
    {
        return $this->smtp_password_decrypted;
    }
}
