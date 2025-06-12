# Academic Year Active Constraint Implementation

## Overview
This document outlines the implementation of the constraint that ensures only one academic year can be active at a time in the college management system.

## Problem Statement
The user requested that "only one active session" should exist, meaning that two active academic year sessions cannot exist simultaneously.

## Implementation Details

### 1. Model Changes (app/Models/AcademicYear.php)

#### Added `setAsActive()` Method
```php
public function setAsActive()
{
    DB::transaction(function () {
        // Set all other academic years as not active
        static::where('is_active', true)->update(['is_active' => false]);

        // Set this academic year as active
        $this->update(['is_active' => true]);
    });
}
```

#### Added `active()` Static Method
```php
public static function active()
{
    return static::where('is_active', true)->first();
}
```

### 2. Controller Changes (app/Http/Controllers/AcademicYearController.php)

#### Updated `store()` Method
- Added logic to deactivate all other academic years when creating a new active year
- Ensures constraint is enforced during creation

#### Updated `update()` Method  
- Added logic to deactivate all other academic years when updating an existing year to active
- Ensures constraint is enforced during updates

#### Added `setActive()` Method
```php
public function setActive(AcademicYear $academicYear)
{
    $this->authorize('manage-settings');

    try {
        $academicYear->setAsActive();
        return redirect()->route('academic-years.index')
            ->with('success', 'Academic year set as active successfully.');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Error setting academic year as active: ' . $e->getMessage());
    }
}
```

### 3. Route Changes (routes/web.php)

#### Added New Route
```php
Route::put('academic-years/{academicYear}/set-active', [AcademicYearController::class, 'setActive'])
    ->name('academic-years.set-active');
```

### 4. View Changes

#### Updated Index View (resources/views/academic-years/index.blade.php)
- Modified the Status column to include a "Set Active" button for inactive academic years
- Added confirmation dialog with warning message
- Enhanced UI to clearly show active/inactive status

#### Updated Create View (resources/views/academic-years/create.blade.php)
- Updated the description for the "Active" checkbox to warn about the constraint
- Clarified that only one academic year can be active at a time

#### Created Edit View (resources/views/academic-years/edit.blade.php)
- New edit form with same constraint warnings
- Properly handles existing values and validation

### 5. Constraint Enforcement Logic

The constraint is enforced at multiple levels:

1. **Application Level**: 
   - Model methods (`setAsActive()`)
   - Controller logic in `store()` and `update()` methods
   - Transaction-based updates to ensure atomicity

2. **User Interface Level**:
   - Clear warnings in forms
   - Confirmation dialogs for actions
   - Visual indicators of active status

## How It Works

### Creating a New Academic Year
1. User fills out the create form
2. If "Active" is checked, the controller automatically deactivates all other academic years
3. The new academic year is created as the only active one

### Updating an Existing Academic Year
1. User edits an academic year and checks "Active"
2. Controller checks if the year is not already active
3. If setting to active, all other active years are deactivated first
4. The selected year becomes the only active one

### Using the "Set Active" Button
1. User clicks "Set Active" button in the index view
2. Confirmation dialog appears with warning
3. If confirmed, the `setActive()` method is called
4. All other years are deactivated, selected year becomes active

## Database Transactions
All operations that modify the active status use database transactions to ensure data consistency and prevent race conditions.

## User Experience Improvements
- Clear visual indicators (green badges for active, gray for inactive)
- Confirmation dialogs with explanatory messages
- Helpful descriptions in forms
- Consistent button styling and iconography

## Testing
A comprehensive test suite has been created (`tests/Feature/AcademicYearActiveConstraintTest.php`) that verifies:
- Only one academic year can be active at a time
- The `setAsActive()` method works correctly
- Controller methods enforce the constraint
- Routes function properly

## Benefits
1. **Data Integrity**: Prevents multiple active academic years
2. **User Clarity**: Clear UI indicators and warnings
3. **Atomic Operations**: Transaction-based updates prevent inconsistent states
4. **Flexibility**: Easy to change active year through multiple interfaces
5. **Maintainability**: Clean, well-documented code with proper separation of concerns

## Future Enhancements
- Database-level constraints (if needed for additional safety)
- Audit logging for active year changes
- Automated tests in CI/CD pipeline
- API endpoints for programmatic access
