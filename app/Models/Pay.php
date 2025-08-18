<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pay extends Model
{
    use HasFactory;

    protected $table = 'pays';

    protected $fillable = [
        'payment_number',
        'table_id',
        'user_id',
        'total',
        'is_type',
        'received_amount',
        'change_amount',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'total' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    /**
     * สร้างเลขใบเสร็จอัตโนมัติแบบต่อเนื่อง
     */
    public static function generatePaymentNumber()
    {
        return DB::transaction(function () {
            // ล็อคตารางเพื่อป้องกันการสร้างเลขซ้ำ
            try {
                // ดึงเลขใบเสร็จล่าสุดจากตาราง pays
                $lastPayment = self::whereNotNull('payment_number')
                                  ->where('payment_number', '!=', '')
                                  ->orderByRaw('CAST(payment_number AS UNSIGNED) DESC')
                                  ->lockForUpdate() // ใช้ lockForUpdate แทน LOCK TABLES
                                  ->first();
                
                if (!$lastPayment || !$lastPayment->payment_number) {
                    // ถ้าไม่มีใบเสร็จก่อนหน้า เริ่มต้นที่ 1
                    $newNumber = 1;
                } else {
                    // ดึงเลขจากใบเสร็จล่าสุดและเพิ่ม 1
                    $lastNumber = intval($lastPayment->payment_number);
                    $newNumber = $lastNumber + 1;
                }
                
                return sprintf('%08d', $newNumber);
                
            } catch (\Exception $e) {
                \Log::error('Generate payment number error: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Boot method สำหรับ auto-generate payment number
     */
    protected static function boot()
    {
        parent::boot();
        
        // สร้างเลขใบเสร็จอัตโนมัติเมื่อสร้างใบเสร็จใหม่
        static::creating(function ($pay) {
            if (empty($pay->payment_number)) {
                $pay->payment_number = self::generatePaymentNumber();
            }
        });
    }

    /**
     * ดึงประเภทการชำระเงิน
     */
    public function getPaymentTypeText()
    {
        switch ($this->is_type) {
            case 0:
                return 'เงินสด';
            case 1:
                return 'เงินโอน';
            default:
                return 'ไม่ระบุ';
        }
    }

    // ความสัมพันธ์อื่นๆ...
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function payGroups()
    {
        return $this->hasMany(PayGroup::class, 'pay_id');
    }

    public function orders()
    {
        return $this->hasManyThrough(
            Orders::class,
            PayGroup::class,
            'pay_id',
            'id',
            'id',
            'order_id'
        );
    }
}