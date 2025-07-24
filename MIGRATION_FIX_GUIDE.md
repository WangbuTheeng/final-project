# Migration Fix Guide

This guide provides solutions for common migration issues encountered in the college management system.

## 🚨 Common Issues

### 1. "Table already exists" Error
```
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'grade_scales' already exists
```

### 2. Foreign Key Constraint Error
```
Cannot drop index 'fees_academic_year_id_is_active_index': needed in a foreign key constraint
```

## 🔧 Solutions

### Option 1: Automated Fix (Recommended)

1. **Run the migration fix script:**
```bash
php scripts/fix-migrations.php
```

2. **Run migrations:**
```bash
php artisan migrate
```

### Option 2: Manual Fix

1. **Check migration status:**
```bash
php artisan migrate:status
```

2. **Fix orphaned tables manually:**
```bash
php artisan tinker
```

In tinker:
```php
// Mark grade_scales migration as run if table exists
if (Schema::hasTable('grade_scales')) {
    DB::table('migrations')->insert([
        'migration' => '2024_01_20_000003_create_grade_scales_table',
        'batch' => DB::table('migrations')->max('batch') + 1
    ]);
}

// Fix other orphaned tables similarly
exit
```

3. **Run migrations:**
```bash
php artisan migrate
```

### Option 3: Fresh Start (Development Only)

⚠️ **Warning: This will delete all data**

```bash
php artisan migrate:fresh --seed
```

## 📋 Files Modified

### 1. Migration Files
- `database/migrations/2024_01_20_000003_create_grade_scales_table.php` - Added table existence check
- `database/migrations/2025_06_17_000000_update_fees_table_structure.php` - Fixed foreign key handling
- `database/migrations/2025_01_24_000000_fix_migration_conflicts.php` - New comprehensive fix

### 2. Helper Scripts
- `scripts/fix-migrations.php` - Automated migration fix script

## 🚀 Deployment Steps

### For Production Server:

1. **Backup database:**
```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

2. **Pull latest changes:**
```bash
git pull origin main
```

3. **Run migration fix:**
```bash
php scripts/fix-migrations.php
```

4. **Run migrations:**
```bash
php artisan migrate --force
```

5. **Verify system:**
```bash
php artisan migrate:status
```

### For Development:

1. **Pull latest changes:**
```bash
git pull origin main
```

2. **Run migration fix:**
```bash
php scripts/fix-migrations.php
```

3. **Run migrations:**
```bash
php artisan migrate
```

## 🔍 Troubleshooting

### If migrations still fail:

1. **Check database connection:**
```bash
php artisan tinker
DB::connection()->getPdo();
```

2. **Check table structure:**
```bash
php artisan tinker
Schema::getColumnListing('grade_scales');
```

3. **Manual cleanup (last resort):**
```bash
php artisan tinker
// Drop problematic tables
Schema::dropIfExists('grade_scales');
// Remove migration records
DB::table('migrations')->where('migration', 'like', '%grade_scales%')->delete();
exit
```

## 📞 Support

If you encounter issues not covered in this guide:

1. Check the error logs: `storage/logs/laravel.log`
2. Run the diagnostic script: `php scripts/fix-migrations.php`
3. Contact the development team with the full error message

## ✅ Verification

After applying fixes, verify everything works:

```bash
# Check migration status
php artisan migrate:status

# Test the application
php artisan serve

# Check specific functionality
php artisan tinker
App\Models\GradingSystem::count();
App\Models\CollegeSetting::first();
```
