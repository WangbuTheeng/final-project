# Custom Grading System Implementation

This document explains the custom grading system feature that has been implemented in the college management system.

## Overview

The custom grading system allows administrators to:
- Create multiple grading systems (TU, CBSE, Simple Letter, Percentage-based, etc.)
- Define custom grade scales for each system
- Select specific grading systems for individual exams
- Generate marksheets using the selected grading system

## Features Implemented

### 1. Grading System Management
- **Location**: Academic Structure → Grading Systems
- **Functionality**: Create, edit, view, and manage grading systems
- **Access**: Admin and Super Admin roles

### 2. Grade Scale Definition
- Define grade letters (A+, A, B+, B, C, D, F, etc.)
- Set percentage ranges for each grade
- Assign grade points for GPA calculation
- Add descriptive remarks for each grade

### 3. Exam Integration
- Select grading system during exam creation
- Option to use default grading system or choose a specific one
- Grading system field is optional (falls back to default if not selected)

### 4. Marksheet Generation
- Marksheets use the exam's selected grading system
- Grade scale table shows the grading system used
- Automatic grade calculation based on the selected system

## Database Structure

### Tables Created/Modified

1. **grading_systems**
   - `id`, `name`, `code`, `description`
   - `status`, `is_default`, `order_sequence`
   - `created_at`, `updated_at`

2. **grade_scales** (modified)
   - Added `grading_system_id` foreign key
   - Unique constraint: `grading_system_id` + `grade_letter`

3. **exams** (modified)
   - Added `grading_system_id` foreign key (nullable)

## Pre-loaded Grading Systems

The system comes with 4 pre-configured grading systems:

### 1. TU Grading System (Default)
- A+: 90-100% (4.0 points) - Outstanding
- A: 80-89% (3.6 points) - Excellent
- B+: 70-79% (3.2 points) - Very Good
- B: 60-69% (2.8 points) - Good
- C+: 50-59% (2.4 points) - Satisfactory
- C: 40-49% (2.0 points) - Acceptable
- D: 32-39% (1.6 points) - Partially Acceptable
- F: 0-31% (0.0 points) - Fail

### 2. CBSE Grading System
- A1: 91-100% (9.99 points) - Outstanding
- A2: 81-90% (9.0 points) - Excellent
- B1: 71-80% (8.0 points) - Very Good
- B2: 61-70% (7.0 points) - Good
- C1: 51-60% (6.0 points) - Fair
- C2: 41-50% (5.0 points) - Satisfactory
- D: 33-40% (4.0 points) - Needs Improvement
- E: 0-32% (0.0 points) - Fail

### 3. Simple Letter Grading
- A: 90-100% (4.0 points) - Excellent
- B: 80-89% (3.0 points) - Good
- C: 70-79% (2.0 points) - Average
- D: 60-69% (1.0 points) - Below Average
- F: 0-59% (0.0 points) - Fail

### 4. Percentage Grading
- A+: 90-100% (4.0 points) - Distinction (90-100%)
- A: 80-89% (3.5 points) - First Class (80-89%)
- B+: 70-79% (3.0 points) - Second Class (70-79%)
- B: 60-69% (2.5 points) - Second Class (60-69%)
- C: 50-59% (2.0 points) - Third Class (50-59%)
- D: 40-49% (1.5 points) - Pass (40-49%)
- F: 0-39% (0.0 points) - Fail (0-39%)

## How to Use

### Creating a New Grading System
1. Navigate to Academic Structure → Grading Systems
2. Click "Create Grading System"
3. Fill in system details (name, code, description)
4. Define grade scales with letters, percentage ranges, and points
5. Save the system

### Using Grading Systems in Exams
1. When creating an exam, select a grading system from the dropdown
2. If no system is selected, the default system will be used
3. The selected system will be used for grade calculation and marksheet generation

### Setting Default Grading System
1. Go to Grading Systems list
2. Click the star icon next to any system to set it as default
3. Only one system can be default at a time

## Technical Implementation

### Models
- `GradingSystem`: Manages grading systems
- `GradeScale`: Manages individual grade scales within systems
- `Exam`: Extended to include grading system relationship

### Key Methods
- `Exam::getEffectiveGradingSystem()`: Gets exam's grading system or default
- `Exam::getGradeByPercentage($percentage)`: Calculates grade using exam's system
- `GradingSystem::getGradeByPercentage($percentage)`: Calculates grade for specific system

### Grade Calculation Flow
1. When marks are entered, percentage is calculated
2. System checks exam's grading system (or uses default)
3. Grade letter and points are determined based on percentage ranges
4. Results are stored and displayed using the selected system

## Benefits

1. **Flexibility**: Support for different educational standards
2. **Customization**: Create systems specific to institution needs
3. **Consistency**: Standardized grading across different exam types
4. **Compliance**: Meet requirements of different educational boards
5. **Transparency**: Clear grade scales shown on marksheets

## Future Enhancements

Potential improvements that could be added:
- Import/export grading systems
- Grade scale validation (no overlapping ranges)
- Bulk assignment of grading systems to exams
- Historical tracking of grading system changes
- Integration with external grading standards
