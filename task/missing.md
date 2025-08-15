# Missing Features & Improvements for Form Builder Tests

## 🚀 **Phase 1: Basic Arguments for Existing Field Types**

### 1.1 Text Field Additional Arguments

- [ ] Add `maxlength` and `minlength` validation
  - Test storage and retrieval
  - Test validation (minlength < maxlength)
  - Test edge cases (0, negative values, empty strings)
- [ ] Add `pattern` (regex) support
  - Test regex pattern storage
  - Test pattern validation
- [ ] Add `size` attribute
  - Test width storage and retrieval
- [ ] Add `readonly` and `disabled` states
  - Test boolean state storage
  - Test state retrieval

### 1.2 Number Field Additional Arguments

- [ ] Extend existing `min`, `max`, `step` tests
  - Add validation (min < max)
  - Test edge cases (negative values, zero, large numbers)
- [ ] Add `readonly` and `disabled` states
- [ ] Add `placeholder` with i18n support

### 1.3 Textarea Field Additional Arguments

- [ ] Extend existing `rows`, `cols` tests
- [ ] Add `maxlength` and `minlength`
- [ ] Add `readonly` and `disabled` states
- [ ] Add `placeholder` with i18n support

## 🔧 **Phase 2: New Field Types**

### 2.1 Password Field

- [ ] Basic configuration test
- [ ] `maxlength`, `minlength`, `pattern` support
- [ ] `autocomplete` attribute
- [ ] `readonly` and `disabled` states

### 2.2 Checkbox Field

- [ ] Basic configuration test
- [ ] `checked` state
- `value` attribute
- [ ] `readonly` and `disabled` states

### 2.3 File Field

- [ ] Basic configuration test
- [ ] `accept` attribute (file types)
- [ ] `multiple` attribute
- [ ] `maxsize` attribute

### 2.4 Date/Time Fields

- [ ] Date field test
- [ ] Time field test
- [ ] Datetime-local field test
- [ ] Min/max date validation

### 2.5 Other HTML5 Fields

- [ ] URL field test
- [ ] Tel field test
- [ ] Search field test
- [ ] Color field test
- [ ] Range field test
- [ ] Hidden field test

## ⚠️ **Phase 3: Error Testing & Validation**

### 3.1 Field Type Validation

- [ ] Test invalid field types
  ```php
  it('fails when field type is not supported', function () {
      // Test 'invalid_type', 'custom_type', etc.
  });
  ```
- [ ] Test missing required field type
- [ ] Test empty field type string

### 3.2 Configuration Validation

- [ ] Test min > max validation
  ```php
  it('fails when min value is greater than max value', function () {
      // Test number field with min: 100, max: 50
  });
  ```
- [ ] Test minlength > maxlength validation
- [ ] Test invalid regex patterns
- [ ] Test invalid file types

### 3.3 i18n Validation

- [ ] Test unregistered locales
  ```php
  it('fails when field has unregistered locale', function () {
      // Test 'fr' locale when only 'en', 'de' are registered
  });
  ```
- [ ] Test missing translations for required locales
- [ ] Test empty translation strings
- [ ] Test null translation values

### 3.4 Relationship Validation

- [ ] Test field without form
- [ ] Test field with invalid form_id
- [ ] Test form deletion cascade

## 🧪 **Phase 4: Test Helpers & Factories**

### 4.1 Form Factory

```php
// Create form with default locales
function createTestForm(array $overrides = []): Form
{
    return Form::create(array_merge([
        'name' => ['en' => 'Test Form', 'de' => 'Test Formular'],
        'description' => ['en' => 'Test Description', 'de' => 'Test Beschreibung'],
        'is_active' => true,
        'configuration' => ['locales' => ['en', 'de']],
    ], $overrides));
}
```

### 4.2 Field Factory

```php
// Create field with default configuration
function createTestField(Form $form, string $type, array $overrides = []): Field
{
    $defaultConfig = [
        'type' => $type,
        'label' => ['en' => 'Test Label', 'de' => 'Test Etikett'],
        'required' => false,
    ];

    return Field::create(array_merge([
        'form_id' => $form->id,
        'configuration' => $defaultConfig,
    ], $overrides));
}
```

### 4.3 Locale Helper

```php
// Get test locales
function getTestLocales(): array
{
    return ['en', 'de'];
}

// Get test translations
function getTestTranslations(string $key): array
{
    $translations = [
        'form_name' => ['en' => 'Test Form', 'de' => 'Test Formular'],
        'field_label' => ['en' => 'Test Field', 'de' => 'Test Feld'],
        // Add more common translations
    ];

    return $translations[$key] ?? ['en' => 'Test', 'de' => 'Test'];
}
```

## 🎯 **Phase 5: Edge Cases & Boundary Testing**

### 5.1 Numeric Boundaries

- [ ] Test extremely large numbers
- [ ] Test negative numbers
- [ ] Test zero values
- [ ] Test decimal precision

### 5.2 String Boundaries

- [ ] Test empty strings
- [ ] Test very long strings
- [ ] Test special characters
- [ ] Test unicode characters

### 5.3 Array Boundaries

- [ ] Test empty arrays
- [ ] Test very large arrays
- [ ] Test nested arrays
- [ ] Test mixed data types

### 5.4 Database Boundaries

- [ ] Test UUID generation
- [ ] Test foreign key constraints
- [ ] Test cascade deletion
- [ ] Test transaction rollback

## 📊 **Phase 6: Performance & Load Testing**

### 6.1 Bulk Operations

- [ ] Test creating many fields at once
- [ ] Test form with many fields
- [ ] Test concurrent field creation

### 6.2 Memory Usage

- [ ] Test large configuration objects
- [ ] Test deep nested structures
- [ ] Test memory leaks

## 🔍 **Phase 7: Integration Testing**

### 7.1 API Endpoints

- [ ] Test field creation via API
- [ ] Test field update via API
- [ ] Test field deletion via API
- [ ] Test validation errors via API

### 7.2 Database Integration

- [ ] Test database constraints
- [ ] Test index performance
- [ ] Test query optimization

## 📝 **Implementation Notes**

### Priority Order:

1. **High**: Error testing & validation (Phase 3)
2. **Medium**: Test helpers & factories (Phase 4)
3. **Medium**: Edge cases (Phase 5)
4. **Low**: Performance testing (Phase 6)
5. **Low**: Integration testing (Phase 7)

### Estimated Time:

- Phase 1: 2-3 hours
- Phase 2: 3-4 hours
- Phase 3: 4-5 hours
- Phase 4: 2-3 hours
- Phase 5: 3-4 hours
- Phase 6: 2-3 hours
- Phase 7: 3-4 hours

**Total: 19-26 hours**

### Dependencies:

- Phase 3 requires Phase 1 & 2 to be complete
- Phase 4 can be done in parallel with Phase 3
- Phase 5 requires Phase 3 to be complete
- Phase 6 & 7 can be done independently

## 🎯 **Next Steps**

1. Commit current working tests
2. Start with Phase 1 (basic arguments)
3. Implement Phase 3 (error testing) in parallel
4. Add test helpers as needed
5. Continue with remaining phases

## 🏗️ **Phase 8: Test Organization & Structure Improvements**

### 8.1 Current Test Structure Issues

- [ ] **Monolithic test files** - svi testovi su u jednom velikom fajlu
- [ ] **Mixed concerns** - CRUD, relationships, configuration sve u istom fajlu
- [ ] **Hard to maintain** - teško je naći specifičan test
- [ ] **Poor separation** - osnovni testovi i kompleksni testovi su pomešani

### 8.2 Proposed Better Organization

#### Option A: Separate by Field Type (Recommended)

```
tests/Feature/Fields/
├── TextFieldTest.php          # text, textarea, password
├── NumberFieldTest.php        # number, range
├── SelectFieldTest.php        # select, radio, checkbox
├── FileFieldTest.php          # file, image
├── DateTimeFieldTest.php      # date, time, datetime-local
├── SpecialFieldTest.php       # url, tel, search, color, hidden
└── BaseFieldTest.php          # Common CRUD operations
```

#### Option B: Separate by Functionality

```
tests/Feature/Fields/
├── FieldCRUDTest.php          # Create, Read, Update, Delete
├── FieldValidationTest.php    # All validation tests
├── FieldRelationshipTest.php  # Form relationships
├── FieldConfigurationTest.php # Configuration storage
└── FieldTypeTest.php          # Type-specific tests
```

#### Option C: Hybrid Approach (Most Flexible)

```
tests/Feature/Fields/
├── Base/
│   ├── FieldCRUDTest.php      # Common CRUD operations
│   ├── FieldValidationTest.php # Common validation
│   └── FieldRelationshipTest.php # Relationships
├── Types/
│   ├── TextFieldsTest.php     # text, textarea, password
│   ├── NumericFieldsTest.php  # number, range
│   ├── ChoiceFieldsTest.php   # select, radio, checkbox
│   ├── FileFieldsTest.php     # file, image
│   └── DateTimeFieldsTest.php # date, time, datetime
└── Integration/
    ├── FieldFormTest.php      # Form + Field integration
    └── FieldAPITest.php       # API endpoint testing
```

### 8.3 Benefits of Better Organization

#### Maintainability

- [ ] **Easier to find tests** - testovi su logički grupisani
- [ ] **Easier to maintain** - manji fajlovi su lakši za održavanje
- [ ] **Better collaboration** - više ljudi može raditi na različitim delovima

#### Readability

- [ ] **Clear purpose** - svaki fajl ima jasnu namenu
- [ ] **Logical grouping** - povezani testovi su zajedno
- [ ] **Easier debugging** - lakše je naći problematičan test

#### Scalability

- [ ] **Easy to add new field types** - samo dodaj novi fajl
- [ ] **Easy to extend existing types** - modifikuj postojeći fajl
- [ ] **Better test isolation** - testovi se ne mešaju

### 8.4 Migration Strategy

#### Step 1: Create New Structure

- [ ] Kreiraj nove direktorijume
- [ ] Premesti postojeće testove u odgovarajuće fajlove
- [ ] Zadrži postojeću funkcionalnost

#### Step 2: Refactor Common Code

- [ ] Izdvoji zajedničke testove u base klase
- [ ] Koristi traits za zajedničku funkcionalnost
- [ ] Kreiraj helper funkcije

#### Step 3: Improve Test Quality

- [ ] Dodaj testove za greške
- [ ] Dodaj edge cases
- [ ] Koristi test helpers

### 8.5 Example Refactored Structure

#### BaseFieldTest.php

```php
abstract class BaseFieldTest extends TestCase
{
    use RefreshDatabase;

    protected function createTestForm(array $overrides = []): Form
    {
        // Common form creation logic
    }

    protected function createTestField(Form $form, string $type, array $overrides = []): Field
    {
        // Common field creation logic
    }

    protected function assertFieldStored(Field $field): void
    {
        // Common assertion logic
    }
}
```

#### TextFieldsTest.php

```php
class TextFieldsTest extends BaseFieldTest
{
    public function test_can_create_text_field(): void
    {
        // Text field specific test
    }

    public function test_can_create_textarea_field(): void
    {
        // Textarea field specific test
    }

    public function test_can_create_password_field(): void
    {
        // Password field specific test
    }
}
```

### 8.6 Implementation Priority

1. **High**: Create new directory structure
2. **High**: Move existing tests to new files
3. **Medium**: Create base classes and helpers
4. **Medium**: Refactor common code
5. **Low**: Add new test categories

### 8.7 Estimated Time for Reorganization

- **Phase 8**: 4-6 hours
- **Total with reorganization**: 23-32 hours

## 🎯 **Updated Next Steps**

1. **Commit current working tests** ✅
2. **Reorganize test structure** (Phase 8) - NEW PRIORITY
3. **Start with Phase 1** (basic arguments)
4. **Implement Phase 3** (error testing) in parallel
5. **Add test helpers** as needed
6. **Continue with remaining phases**
