# Implementation Plan: CMS Homepage Integration

## Overview

This implementation plan breaks down the CMS Homepage Integration feature into discrete coding tasks. The approach follows an incremental pattern: backend controller updates first, then frontend component updates, followed by testing. Each task builds on previous work to ensure the system remains functional throughout development.

## Tasks

- [x] 1. Update Homepage route to use CmsSettingController
  - Modify `routes/web.php` to replace the closure with controller method
  - Change from `function () { return Inertia::render('Homepage'); }` to `[CmsSettingController::class, 'homepage']`
  - Add route name `->name('homepage')` for consistency
  - Maintain existing middleware: `auth` and `UserMiddleware`
  - _Requirements: 2.1, 2.2, 2.3_

- [x] 2. Implement CmsSettingController::homepage() method
  - Add new `homepage()` method to `app/Http/Controllers/CmsSettingController.php`
  - Query database for first Setting_Record using `CmsSetting::first()`
  - Construct response with image_url attribute using array_merge pattern
  - Return Inertia response rendering 'Homepage' with setting data or null
  - Follow the existing `landing()` method pattern for consistency
  - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [ ]* 2.1 Write property test for homepage() controller method
  - **Property 3: Controller Returns First Record**
  - **Validates: Requirements 2.1, 2.2**
  - Generate random Setting_Records, insert into test database
  - Call homepage() method and assert it returns first record with image_url
  - Test with empty database and assert null is returned
  - Run minimum 100 iterations

- [x] 3. Update Homepage.vue to use dynamic content
  - [x] 3.1 Add computed properties for dynamic content
    - Create `heroTitle` computed property with fallback to default
    - Create `heroDescription` computed property with fallback to default
    - Create `heroBackgroundImage` computed property with fallback to '/img/pacitanpantai.jpg'
    - Create `testimonialContent` computed property with fallback to default
    - Use optional chaining (`props.setting?.field`) for null safety
    - Use nullish coalescing (`||`) for default values
    - _Requirements: 3.1, 3.2, 3.3, 4.1, 8.1, 8.2, 8.3, 8.4_

  - [x] 3.2 Update hero section template to use computed properties
    - Replace hardcoded title with `{{ heroTitle }}`
    - Replace hardcoded description with `{{ heroDescription }}`
    - Replace static background-image with `:style` binding using `heroBackgroundImage`
    - Maintain existing CSS classes and styling
    - _Requirements: 3.1, 3.2, 3.3, 3.5_

  - [x] 3.3 Update testimonial section template to use computed properties
    - Replace hardcoded testimonial with `{{ testimonialContent }}`
    - Maintain existing CSS classes and styling
    - Keep the attribution "- Rina, Jakarta" as static
    - _Requirements: 4.1, 4.3_

- [ ]* 3.4 Write property test for Homepage.vue dynamic rendering
  - **Property 4: Dynamic Content Rendering**
  - **Validates: Requirements 3.1, 3.2, 3.3, 4.1, 5.2**
  - Generate random Setting_Record data
  - Mount Homepage component with setting prop
  - Assert nama appears in hero title element
  - Assert deskripsi appears in hero description element
  - Assert image_url appears in background-image style
  - Assert footer appears in testimonial section
  - Run minimum 100 iterations

- [ ]* 3.5 Write property test for null safety
  - **Property 10: Null Safety and Graceful Degradation**
  - **Validates: Requirements 3.4, 4.2, 5.3, 8.1, 8.2, 8.3, 8.4**
  - Generate various null/undefined states (null setting, null image_url, null footer)
  - Mount Homepage component with each state
  - Assert component renders without errors
  - Assert default values are displayed
  - Run minimum 100 iterations

- [x] 4. Checkpoint - Test the integration manually
  - Ensure all tests pass, ask the user if questions arise
  - Verify Homepage displays default content when no settings exist
  - Create a test Setting_Record via admin panel
  - Verify Homepage displays the dynamic content
  - Update the Setting_Record and verify changes appear on Homepage

- [ ]* 5. Write backend property tests for data persistence
  - [ ]* 5.1 Write property test for data persistence round trip
    - **Property 1: Data Persistence Round Trip**
    - **Validates: Requirements 1.1, 1.2, 1.3, 1.4**
    - Generate random Setting_Record data (nama, deskripsi, image, footer)
    - Create record using CmsSetting::create()
    - Retrieve record and assert all fields match original data
    - Run minimum 100 iterations

  - [ ]* 5.2 Write property test for image URL accessor
    - **Property 2: Image URL Construction**
    - **Validates: Requirements 1.5, 5.1**
    - Generate random image paths (including null)
    - Create Setting_Record with each path
    - Assert image_url accessor constructs correct full URL
    - Assert image_url is null when image is null
    - Run minimum 100 iterations

- [ ]* 6. Write backend property tests for validation
  - [ ]* 6.1 Write property test for required field validation
    - **Property 5: Required Field Validation**
    - **Validates: Requirements 7.1, 7.2**
    - Generate requests with/without nama and deskripsi
    - Assert validation fails when either field is missing
    - Assert validation passes when both fields are present
    - Run minimum 100 iterations

  - [ ]* 6.2 Write property test for image file validation
    - **Property 6: Image File Validation**
    - **Validates: Requirements 7.3, 7.4**
    - Generate files of various types and sizes
    - Assert validation fails for non-image files
    - Assert validation fails for files exceeding 2048KB
    - Assert validation passes for valid images under size limit
    - Run minimum 100 iterations

  - [ ]* 6.3 Write property test for optional footer field
    - **Property 7: Optional Footer Field**
    - **Validates: Requirements 7.5**
    - Generate requests with valid nama and deskripsi but without footer
    - Assert validation passes
    - Assert created record has footer as null
    - Run minimum 100 iterations

- [ ]* 7. Write backend property tests for CRUD operations
  - [ ]* 7.1 Write property test for CRUD operations integrity
    - **Property 8: CRUD Operations Integrity**
    - **Validates: Requirements 6.1, 6.2, 6.3**
    - Generate random Setting_Record data
    - Test create operation and assert record exists in database
    - Test update operation and assert changes are persisted
    - Test delete operation and assert record is removed
    - Run minimum 100 iterations

  - [ ]* 7.2 Write property test for image storage location
    - **Property 9: Image Storage Location**
    - **Validates: Requirements 6.4**
    - Generate random valid image files
    - Upload via store() method
    - Assert file exists in storage/app/public/cmssettings/
    - Assert database contains correct relative path
    - Run minimum 100 iterations

- [ ]* 8. Write unit tests for edge cases
  - [ ]* 8.1 Write unit test for empty database scenario
    - Clear database of all Setting_Records
    - Call homepage() method
    - Assert null is returned
    - Mount Homepage component with null setting
    - Assert default content is displayed
    - _Requirements: 2.3, 8.1_

  - [ ]* 8.2 Write unit test for null image field
    - Create Setting_Record with image as null
    - Call homepage() method
    - Assert image_url is null in response
    - Mount Homepage component
    - Assert default background image is used
    - _Requirements: 5.3, 8.2_

  - [ ]* 8.3 Write unit test for null footer field
    - Create Setting_Record with footer as null
    - Mount Homepage component
    - Assert default testimonial is displayed
    - _Requirements: 4.2, 8.3_

  - [ ]* 8.4 Write unit test for validation error messages
    - Submit create request without nama
    - Assert validation error message is returned
    - Submit create request without deskripsi
    - Assert validation error message is returned
    - _Requirements: 7.1, 7.2_

  - [ ]* 8.5 Write unit test for image upload and deletion
    - Upload image via store() method
    - Assert file exists in storage
    - Delete Setting_Record via destroy() method
    - Assert file is removed from storage
    - _Requirements: 6.3, 6.4_

- [ ] 9. Final checkpoint - Comprehensive testing
  - Ensure all tests pass (unit and property tests)
  - Verify no regressions in existing functionality
  - Test admin panel CRUD operations work correctly
  - Test Homepage displays dynamic content correctly
  - Test graceful degradation with missing data
  - Ask the user if questions arise

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation at key milestones
- Property tests validate universal correctness properties across many inputs
- Unit tests validate specific examples, edge cases, and error conditions
- The implementation maintains backward compatibility with existing code
- No database schema changes are required
- Existing CmsSetting model and admin panel functionality remain unchanged
