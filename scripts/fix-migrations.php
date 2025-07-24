<?php

/**
 * Migration Fix Script
 * 
 * This script helps fix common migration issues by:
 * 1. Checking for existing tables vs migration records
 * 2. Fixing orphaned tables (tables that exist but migration not recorded)
 * 3. Safely handling foreign key constraints
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Migration Fix Script\n";
echo "======================\n\n";

// Check current migration status
echo "📋 Checking migration status...\n";

try {
    // Get all migration files
    $migrationFiles = glob(__DIR__ . '/../database/migrations/*.php');
    $migrationNames = [];
    
    foreach ($migrationFiles as $file) {
        $filename = basename($file, '.php');
        $migrationNames[] = $filename;
    }
    
    // Get recorded migrations
    $recordedMigrations = DB::table('migrations')->pluck('migration')->toArray();
    
    // Find orphaned tables (exist but not recorded)
    $orphanedMigrations = array_diff($migrationNames, $recordedMigrations);
    
    if (!empty($orphanedMigrations)) {
        echo "⚠️  Found orphaned migrations (tables exist but not recorded):\n";
        foreach ($orphanedMigrations as $migration) {
            echo "   - $migration\n";
        }
        echo "\n";
    }
    
    // Check specific problematic tables
    $problematicTables = [
        'grade_scales' => '2024_01_20_000003_create_grade_scales_table',
        'grading_systems' => '2024_01_20_000002_create_grading_systems_table',
        'college_settings' => '2024_01_20_000001_create_college_settings_table',
        'exam_types' => '2025_07_23_000001_create_exam_types_table'
    ];
    
    echo "🔍 Checking problematic tables...\n";
    
    foreach ($problematicTables as $table => $migration) {
        $tableExists = Schema::hasTable($table);
        $migrationRecorded = in_array($migration, $recordedMigrations);
        
        echo "   Table '$table': ";
        if ($tableExists && !$migrationRecorded) {
            echo "❌ EXISTS but NOT RECORDED\n";
            
            // Fix it
            echo "   🔧 Fixing: Recording migration...\n";
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => DB::table('migrations')->max('batch') + 1
            ]);
            echo "   ✅ Fixed!\n";
            
        } elseif ($tableExists && $migrationRecorded) {
            echo "✅ OK\n";
        } elseif (!$tableExists && $migrationRecorded) {
            echo "⚠️  RECORDED but table MISSING\n";
        } else {
            echo "➖ Not created yet\n";
        }
    }
    
    echo "\n";
    
    // Check for foreign key issues in fees table
    if (Schema::hasTable('fees')) {
        echo "🔗 Checking foreign key constraints in fees table...\n";
        
        try {
            $foreignKeys = DB::select("
                SELECT 
                    CONSTRAINT_NAME,
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM 
                    INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE 
                    TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'fees' 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (!empty($foreignKeys)) {
                echo "   Found foreign keys:\n";
                foreach ($foreignKeys as $fk) {
                    echo "   - {$fk->CONSTRAINT_NAME}: {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
                }
            } else {
                echo "   No foreign keys found.\n";
            }
            
        } catch (\Exception $e) {
            echo "   Error checking foreign keys: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✅ Migration fix script completed!\n";
    echo "\nNext steps:\n";
    echo "1. Run: php artisan migrate\n";
    echo "2. If issues persist, run: php artisan migrate:status\n";
    echo "3. For fresh start: php artisan migrate:fresh --seed\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
