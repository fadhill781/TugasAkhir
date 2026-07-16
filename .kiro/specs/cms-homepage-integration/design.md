# Design Document: CMS Homepage Integration

## Overview

This design implements a connection between the CMS Settings admin panel and the Homepage dynamic content display. The solution leverages the existing CmsSetting model, controller, and admin interface to enable administrators to manage homepage content (hero section and testimonials) without code changes.

The architecture follows Laravel's MVC pattern with Inertia.js for seamless Vue.js integration. The design reuses the existing `landing()` method pattern from CmsSettingController, applying it to the Homepage route to pass CMS settings data to the frontend.

**Key Design Decisions:**
- Reuse existing CmsSetting model and table structure (no schema changes required)
- Map existing fields to homepage sections: `nama` → hero title, `deskripsi` → hero description, `image` → hero background, `footer` → testimonial content
- Use first Setting_Record for homepage content (simple, single-configuration approach)
- Implement graceful fallbacks to default content when no settings exist
- Maintain existing UI/UX design and styling

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                     Browser (Client)                         │
│  ┌────────────────┐              ┌─────────────────────┐   │
│  │  Admin Panel   │              │     Homepage        │   │
│  │  (Setting.vue) │              │  (Homepage.vue)     │   │
│  └────────┬───────┘              └──────────┬──────────┘   │
└───────────┼──────────────────────────────────┼──────────────┘
            │                                  │
            │ Inertia.js                       │ Inertia.js
            │                                  │
┌───────────┼──────────────────────────────────┼──────────────┐
│           │         Laravel Backend          │              │
│  ┌────────▼────────────────┐    ┌───────────▼──────────┐  │
│  │  CmsSettingController   │    │   Homepage Route     │  │
│  │  - create()             │    │   (web.php)          │  │
│  │  - store()              │    │                      │  │
│  │  - edit()               │    └───────────┬──────────┘  │
│  │  - update()             │                │              │
│  │  - destroy()            │                │              │
│  │  - cmssetting()         │                │              │
│  │  + homepage() [NEW]     │◄───────────────┘              │
│  └────────┬────────────────┘                               │
│           │                                                 │
│  ┌────────▼────────────────┐                               │
│  │    CmsSetting Model     │                               │
│  │  - nama                 │                               │
│  │  - deskripsi            │                               │
│  │  - image                │                               │
│  │  - footer               │                               │
│  │  - getImageUrlAttribute │                               │
│  └────────┬────────────────┘                               │
└───────────┼──────────────────────────────────────────────────┘
            │
┌───────────▼──────────────────────────────────────────────────┐
│                    Database (MySQL)                          │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              cms_settings table                      │   │
│  │  - id                                                │   │
│  │  - nama (hero title)                                 │   │
│  │  - deskripsi (hero description)                      │   │
│  │  - image (hero background path)                      │   │
│  │  - footer (testimonial content)                      │   │
│  │  - created_at                                        │   │
│  │  - updated_at                                        │   │
│  └──────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────┘
```

### Data Flow

**Admin Content Management Flow:**
1. Administrator accesses Admin Panel (Setting.vue)
2. Administrator creates/edits Setting_Record via CmsSettingController
3. Data validated and stored in cms_settings table
4. Images uploaded to storage/cmssettings directory
5. Success response returned to Admin Panel

**Homepage Display Flow:**
1. User accesses /homepage route
2. Route handler calls CmsSettingController::homepage()
3. Controller retrieves first Setting_Record from database
4. Controller constructs response with image_url attribute
5. Inertia renders Homepage.vue with setting prop
6. Homepage.vue displays dynamic content or falls back to defaults

## Components and Interfaces

### Backend Components

#### CmsSettingController::homepage() Method

**Purpose:** Retrieve CMS settings and pass to Homepage view

**Method Signature:**
```php
public function homepage(): \Inertia\Response
```

**Implementation Logic:**
```php
public function homepage()
{
    $setting = CmsSetting::first();

    return Inertia::render('Homepage', [
        'setting' => $setting ? array_merge($setting->toArray(), [
            'image_url' => $setting->image_url
        ]) : null,
    ]);
}
```

**Behavior:**
- Queries database for first Setting_Record using `CmsSetting::first()`
- If record exists, merges model array with `image_url` accessor
- If no record exists, passes `null` to view
- Returns Inertia response rendering Homepage.vue

#### Route Definition Update

**Current Route:**
```php
Route::middleware(['auth', UserMiddleware::class])->get('/homepage', function () {
    return Inertia::render('Homepage');
});
```

**Updated Route:**
```php
Route::middleware(['auth', UserMiddleware::class])
    ->get('/homepage', [CmsSettingController::class, 'homepage'])
    ->name('homepage');
```

**Changes:**
- Replace closure with controller method reference
- Add route name for consistency
- Maintain existing middleware (auth, UserMiddleware)

### Frontend Components

#### Homepage.vue Updates

**Props Interface:**
```typescript
interface Props {
  setting: {
    id: number;
    nama: string;
    deskripsi: string;
    image: string | null;
    image_url: string | null;
    footer: string | null;
    created_at: string;
    updated_at: string;
  } | null;
}
```

**Computed Properties:**

```javascript
const heroTitle = computed(() => {
  return props.setting?.nama || 'Liburan Bersama Iconic';
});

const heroDescription = computed(() => {
  return props.setting?.deskripsi || 
    'Liburan menyenangkan bersama iconic. Promo tersedia untuk paket wisata ke bromo. bisa bersama keluarga ataupun rekan kerja';
});

const heroBackgroundImage = computed(() => {
  return props.setting?.image_url || '/img/pacitanpantai.jpg';
});

const testimonialContent = computed(() => {
  return props.setting?.footer || 
    '"Layanan sangat profesional, perjalanan jadi lebih menyenangkan!"';
});
```

**Template Updates:**

Hero Section:
```vue
<section 
  class="relative w-screen h-screen flex flex-col items-center justify-center text-black text-center px-6 bg-cover bg-center" 
  :style="`background-image: url('${heroBackgroundImage}');`"
>
  <h1 class="text-4xl text-white font-bold p-2 m-2">{{ heroTitle }}</h1>
  <p class="mt-3 text-white text-lg p-2 m-2">{{ heroDescription }}</p>
  <button class="px-4 py-2 my-2 flex cursor-pointer items-center rounded-md
    border-2 border-black bg-white font-bold text-black
    shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all hover:translate-x-[4px]
    hover:translate-y-[4px] hover:shadow-none">
    Cek Paket Wisata
  </button>
</section>
```

Testimonial Section:
```vue
<section>
  <div class="h-screen w-screen">
    <h2 class="text-3xl font-bold text-center mb-8">Testimoni Pelanggan</h2>
    <div class="container mx-auto px-6 text-center">
      <p class="text-lg italic">{{ testimonialContent }}</p>
      <p class="mt-3 font-bold">- Rina, Jakarta</p>
    </div>
  </div>
</section>
```

## Data Models

### CmsSetting Model

**Existing Structure (No Changes Required):**

```php
class CmsSetting extends Model
{
    protected $table = 'cms_settings';

    protected $fillable = [
        'nama',
        'deskripsi',
        'image',
        'footer',
    ];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
```

**Field Mapping to Homepage:**
- `nama` → Hero section title
- `deskripsi` → Hero section description
- `image` → Hero background image path (stored in storage/cmssettings/)
- `footer` → Testimonial content
- `image_url` (accessor) → Full URL for hero background image

**Database Schema (Existing):**
```sql
CREATE TABLE cms_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    image VARCHAR(255) NULL,
    footer VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Data Validation Rules

**Existing Validation (CmsSettingController):**
```php
$validated = $request->validate([
    'nama' => 'required|string|max:255',
    'deskripsi' => 'required|string',
    'image' => 'nullable|image|max:2048',
    'footer' => 'nullable|string|max:255',
]);
```

**Validation Behavior:**
- `nama`: Required, string, max 255 characters
- `deskripsi`: Required, string, no length limit (TEXT field)
- `image`: Optional, must be image file, max 2MB
- `footer`: Optional, string, max 255 characters



## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Data Persistence Round Trip

*For any* valid Setting_Record data (nama, deskripsi, image path, footer), creating a record and then retrieving it should return all field values unchanged.

**Validates: Requirements 1.1, 1.2, 1.3, 1.4**

### Property 2: Image URL Construction

*For any* Setting_Record with a non-null image path, the image_url accessor should construct a full URL in the format `{app_url}/storage/{image_path}`, and when image is null, image_url should be null.

**Validates: Requirements 1.5, 5.1**

### Property 3: Controller Returns First Record

*For any* database state containing one or more Setting_Records, calling the homepage() controller method should return the first record with the image_url attribute included.

**Validates: Requirements 2.1, 2.2**

### Property 4: Dynamic Content Rendering

*For any* Setting_Record passed to Homepage.vue, the rendered output should contain the nama value in the hero title element, the deskripsi value in the hero description element, the image_url in the background-image style, and the footer value in the testimonial section.

**Validates: Requirements 3.1, 3.2, 3.3, 4.1, 5.2**

### Property 5: Required Field Validation

*For any* create or update request missing either nama or deskripsi fields, the validation should fail and return an error, while requests with both required fields should pass validation (assuming other constraints are met).

**Validates: Requirements 7.1, 7.2**

### Property 6: Image File Validation

*For any* uploaded file, if it is not a valid image type or exceeds 2048KB, validation should fail, while valid image files under the size limit should pass validation.

**Validates: Requirements 7.3, 7.4**

### Property 7: Optional Footer Field

*For any* create or update request with valid nama and deskripsi but without a footer field, the validation should pass and the record should be created/updated with footer as null.

**Validates: Requirements 7.5**

### Property 8: CRUD Operations Integrity

*For any* valid Setting_Record data, the system should support creating a new record (store), updating an existing record (update), and deleting a record (destroy), with each operation correctly persisting changes to the database.

**Validates: Requirements 6.1, 6.2, 6.3**

### Property 9: Image Storage Location

*For any* valid image file uploaded through the Admin_Panel, the file should be stored in the `storage/app/public/cmssettings/` directory and the database should contain the relative path `cmssettings/{filename}`.

**Validates: Requirements 6.4**

### Property 10: Null Safety and Graceful Degradation

*For any* Homepage.vue component state where the setting prop is null, undefined, or contains null fields (image_url, footer), the component should render without errors and display default fallback values for all content sections.

**Validates: Requirements 3.4, 4.2, 5.3, 8.1, 8.2, 8.3, 8.4**

## Error Handling

### Backend Error Handling

**Validation Errors:**
- When validation fails, return 422 Unprocessable Entity with error details
- Display validation errors in admin panel form
- Preserve user input on validation failure

**Database Errors:**
- Catch database exceptions during CRUD operations
- Log errors for debugging
- Return user-friendly error messages
- Rollback transactions on failure

**File Upload Errors:**
- Validate file type and size before processing
- Handle storage failures gracefully
- Clean up partial uploads on error
- Return specific error messages (file too large, invalid type, etc.)

**Missing Data Handling:**
- When no Setting_Record exists, return null gracefully
- Frontend handles null setting prop without errors
- Display default content when data is missing

### Frontend Error Handling

**Null/Undefined Prop Handling:**
- Use optional chaining (`props.setting?.field`) to safely access nested properties
- Provide default values in computed properties using nullish coalescing (`||`)
- Ensure all template bindings handle null values

**Image Loading Errors:**
- If image_url is invalid or image fails to load, browser will handle gracefully
- Consider adding error handling for broken images in future iterations
- Default background image serves as fallback

**Computed Property Safety:**
```javascript
const heroTitle = computed(() => {
  return props.setting?.nama || 'Liburan Bersama Iconic';
});
```
This pattern ensures:
- No errors if `props.setting` is null/undefined
- Always returns a valid string value
- Provides meaningful default content

## Testing Strategy

### Dual Testing Approach

This feature requires both unit tests and property-based tests for comprehensive coverage:

**Unit Tests** focus on:
- Specific examples of valid Setting_Record data
- Edge cases (empty database, null fields, missing images)
- Integration between controller and model
- Validation error messages
- File upload success/failure scenarios

**Property-Based Tests** focus on:
- Universal properties across all valid inputs
- Data persistence round trips with random data
- Validation rules with generated test cases
- Null safety with various null/undefined combinations
- CRUD operations with randomized data

### Property-Based Testing Configuration

**Library Selection:**
- **PHP Backend**: Use `eris/eris` or `giorgiosironi/eris` for property-based testing
- **JavaScript Frontend**: Use `fast-check` for Vue component testing

**Test Configuration:**
- Minimum 100 iterations per property test
- Each test tagged with feature name and property number
- Tag format: `@test Feature: cms-homepage-integration, Property {N}: {property_text}`

**Property Test Implementation:**

Each correctness property must be implemented as a single property-based test:

1. **Property 1 Test**: Generate random Setting_Record data, create record, retrieve it, assert all fields match
2. **Property 2 Test**: Generate random image paths (including null), assert image_url construction is correct
3. **Property 3 Test**: Generate random Setting_Records, insert into database, call homepage(), assert first record returned
4. **Property 4 Test**: Generate random Setting_Record, render Homepage component, assert all fields appear in output
5. **Property 5 Test**: Generate requests with/without required fields, assert validation behaves correctly
6. **Property 6 Test**: Generate files of various types and sizes, assert validation behaves correctly
7. **Property 7 Test**: Generate requests with/without footer, assert validation passes and footer is null when omitted
8. **Property 8 Test**: Generate random Setting_Record, perform create/update/delete, assert database state is correct
9. **Property 9 Test**: Generate random image files, upload via controller, assert storage location is correct
10. **Property 10 Test**: Generate various null/undefined states, render Homepage, assert no errors and defaults shown

### Unit Testing Strategy

**Backend Unit Tests (PHPUnit):**

Test Coverage:
- `CmsSettingController::homepage()` returns correct data structure
- `CmsSettingController::homepage()` returns null when no records exist
- `CmsSettingController::store()` creates record with valid data
- `CmsSettingController::update()` updates existing record
- `CmsSettingController::destroy()` deletes record and associated image
- `CmsSetting::getImageUrlAttribute()` constructs correct URL
- Validation rules reject invalid data
- Image upload stores file in correct directory

**Frontend Unit Tests (Vitest + Vue Test Utils):**

Test Coverage:
- Homepage.vue renders with valid setting prop
- Homepage.vue renders with null setting prop (shows defaults)
- Computed properties return correct values
- Computed properties return defaults when setting is null
- Hero section displays dynamic content
- Testimonial section displays dynamic content
- Background image style is set correctly

### Integration Testing

**End-to-End Flow Tests:**
1. Admin creates Setting_Record → Homepage displays new content
2. Admin updates Setting_Record → Homepage reflects changes
3. Admin deletes Setting_Record → Homepage shows defaults
4. Admin uploads image → Homepage displays new background
5. User accesses Homepage with no settings → Defaults displayed without errors

### Test Data Generators

**For Property-Based Tests:**

PHP Generators (using eris):
```php
// Generate random Setting_Record data
Generator\associative([
    'nama' => Generator\string(),
    'deskripsi' => Generator\string(),
    'image' => Generator\oneOf(
        Generator\constant(null),
        Generator\string()
    ),
    'footer' => Generator\oneOf(
        Generator\constant(null),
        Generator\string()
    ),
]);
```

JavaScript Generators (using fast-check):
```javascript
// Generate random setting prop
fc.record({
  id: fc.integer({ min: 1 }),
  nama: fc.string({ minLength: 1 }),
  deskripsi: fc.string({ minLength: 1 }),
  image: fc.option(fc.string(), { nil: null }),
  image_url: fc.option(fc.webUrl(), { nil: null }),
  footer: fc.option(fc.string(), { nil: null }),
});
```

### Edge Cases to Test

1. **Empty Database**: No Setting_Records exist
2. **Null Image**: Setting_Record exists but image is null
3. **Null Footer**: Setting_Record exists but footer is null
4. **All Nulls**: Setting_Record with only required fields (nama, deskripsi)
5. **Large Files**: Image upload exceeding 2048KB
6. **Invalid File Types**: Non-image files uploaded
7. **Missing Required Fields**: Requests without nama or deskripsi
8. **Special Characters**: Setting_Record with special characters in text fields
9. **Very Long Text**: deskripsi with thousands of characters
10. **Concurrent Updates**: Multiple admins updating same Setting_Record

### Test Execution

**Running Tests:**
```bash
# Backend property tests
./vendor/bin/phpunit --group=property-based

# Backend unit tests
./vendor/bin/phpunit --group=unit

# Frontend property tests
npm run test:property

# Frontend unit tests
npm run test:unit

# All tests
npm run test
```

**Continuous Integration:**
- Run all tests on every commit
- Require tests to pass before merging
- Track test coverage (aim for >80% coverage)
- Run property tests with increased iterations (1000+) in CI
