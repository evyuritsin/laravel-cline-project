# Move-in/out Inspection Mobile App - Flutter Documentation

## 1. Project Overview

**Project Name:** Inspection App  
**Platforms:** Android, iOS  
**Framework:** Flutter 3.x  
**State Management:** flutter_bloc (BLoC pattern)  
**Architecture:** Clean Architecture (Presentation / Domain / Data)

## 2. API Integration

### Base Configuration
- **Base URL:** `http://localhost:8000/api`
- **Authentication:** Bearer Token (from SMS OTP Login)
- **Content-Type:** `application/json`

### Authentication Flow (SMS OTP)

```
1. User enters phone number
2. API sends SMS with 6-digit code
3. User enters code
4. API verifies code and returns token
```

### Endpoints Reference

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | /auth/request-otp | Send OTP to phone | No |
| POST | /auth/verify-otp | Verify OTP and login | No |
| POST | /auth/logout | Logout | Yes |
| GET | /user/profile | Get user profile | Yes |
| PUT | /user/profile | Update profile | Yes |
| GET | /inspections | List inspections | Yes |
| POST | /inspections | Create inspection | Yes |
| GET | /inspections/{id} | Get inspection details | Yes |
| PUT | /inspections/{id} | Update inspection | Yes |
| DELETE | /inspections/{id} | Delete inspection | Yes |
| GET | /inspections/{id}/pdf | Generate PDF report | Yes |
| POST | /inspections/{id}/send | Send PDF via email/SMS | Yes |
| POST | /inspections/{id}/rooms | Add room to inspection | Yes |
| PUT | /rooms/{id} | Update room | Yes |
| DELETE | /rooms/{id} | Delete room | Yes |
| POST | /rooms/{id}/photos | Upload photo | Yes |
| DELETE | /photos/{id} | Delete photo | Yes |
| GET | /geocode | Address to coordinates | No |
| GET | /reverse-geocode | Coordinates to address | No |

### OTP Request/Response

**Request OTP:**
```json
POST /api/auth/request-otp
{
  "phone": "+79001234567"
}
```

**Verify OTP:**
```json
POST /api/auth/verify-otp
{
  "phone": "+79001234567",
  "code": "123456"
}
```

## 3. Clean Architecture Structure

```
lib/
├── main.dart
├── app.dart
├── core/
│   ├── constants/
│   │   ├── api_constants.dart
│   │   ├── app_colors.dart
│   │   ├── app_strings.dart
│   │   └── app_styles.dart
│   ├── errors/
│   │   ├── exceptions.dart
│   │   └── failures.dart
│   ├── network/
│   │   ├── api_client.dart
│   │   ├── api_interceptor.dart
│   │   └── network_info.dart
│   ├── usecases/
│   │   └── usecase.dart
│   └── utils/
│       ├── input_converter.dart
│       └── date_formatter.dart
├── features/
│   ├── auth/
│   │   ├── data/
│   │   │   ├── datasources/
│   │   │   │   └── auth_remote_datasource.dart
│   │   │   ├── models/
│   │   │   │   └── user_model.dart
│   │   │   └── repositories/
│   │   │       └── auth_repository_impl.dart
│   │   ├── domain/
│   │   │   ├── entities/
│   │   │   │   └── user.dart
│   │   │   ├── repositories/
│   │   │   │   └── auth_repository.dart
│   │   │   └── usecases/
│   │   │       ├── request_otp.dart
│   │   │       ├── verify_otp.dart
│   │   │       └── logout.dart
│   │   └── presentation/
│   │       ├── bloc/
│   │       │   ├── auth_bloc.dart
│   │       │   ├── auth_event.dart
│   │       │   └── auth_state.dart
│   │       ├── pages/
│   │       │   └── login_page.dart
│   │       └── widgets/
│   │           ├── phone_input_field.dart
│   │           ├── otp_input_field.dart
│   │           └── country_code_picker.dart
│   ├── inspections/
│   │   ├── data/
│   │   │   ├── datasources/
│   │   │   │   └── inspection_remote_datasource.dart
│   │   │   ├── models/
│   │   │   │   ├── inspection_model.dart
│   │   │   │   ├── room_model.dart
│   │   │   │   └── photo_model.dart
│   │   │   └── repositories/
│   │   │       └── inspection_repository_impl.dart
│   │   ├── domain/
│   │   │   ├── entities/
│   │   │   │   ├── inspection.dart
│   │   │   │   ├── room.dart
│   │   │   │   └── photo.dart
│   │   │   ├── repositories/
│   │   │   │   └── inspection_repository.dart
│   │   │   └── usecases/
│   │   │       ├── get_inspections.dart
│   │   │       ├── create_inspection.dart
│   │   │       ├── update_inspection.dart
│   │   │       ├── delete_inspection.dart
│   │   │       └── generate_pdf.dart
│   │   └── presentation/
│   │       ├── bloc/
│   │       │   ├── inspection_bloc.dart
│   │       │   ├── inspection_event.dart
│   │       │   └── inspection_state.dart
│   │       ├── pages/
│   │       │   ├── inspection_list_page.dart
│   │       │   ├── inspection_detail_page.dart
│   │       │   └── create_inspection_page.dart
│   │       └── widgets/
│   │           ├── inspection_card.dart
│   │           ├── room_list_tile.dart
│   │           └── photo_grid.dart
│   ├── rooms/
│   │   └── [similar structure]
│   ├── geolocation/
│   │   └── [similar structure]
│   └── profile/
│       └── [similar structure]
└── injection_container.dart
```

## 4. Screen Architecture

### Navigation Flow

```
App Launch
    │
    ▼
┌─────────────────┐
│   Splash Screen │
└────────┬────────┘
         │
         ▼
┌─────────────────┐     ┌─────────────────┐
│   Login Page    │────▶│  Home (No Auth) │
│ (Telegram Auth) │     │  Show Login UI   │
└────────┬────────┘     └─────────────────┘
         │ (on success)
         ▼
┌─────────────────┐
│   Home Page     │
│  (Tab Bar Nav)  │
└────────┬────────┘
         │
    ┌────┴────┬──────────┬──────────┐
    ▼         ▼          ▼          ▼
┌───────┐ ┌───────┐ ┌───────┐ ┌───────┐
│Inspect│ │ Map   │ │Reports│ │Profile│
│ -List │ │(Future│ │(PDF)  │ │ -Edit │
│ -New  │ │Feature│ │       │ │ -Tier │
└───┬───┘ └───────┘ └───────┘ └───────┘
    │
    ▼
┌─────────────────┐
│ Inspection      │
│ Detail Page     │
│ - Rooms List    │
│ - Add Room Btn │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Room Detail     │
│ - Photos Grid   │
│ - Add Photo Btn │
│ - Elements List │
└─────────────────┘
```

### Screen States

Each screen implements three states:

1. **Initial** - Show skeleton or empty state
2. **Loading** - Show loading indicator
3. **Loaded** - Show content
4. **Error** - Show error message with retry button

## 5. GUI Design

### Color Palette

| Name | Hex | Usage |
|------|-----|-------|
| Primary | #2563EB | App bar, buttons, links |
| Primary Dark | #1D4ED8 | Status bar, pressed states |
| Secondary | #10B981 | Success states, completed items |
| Accent | #F59E0B | Warnings, pending states |
| Error | #EF4444 | Error states, delete actions |
| Background | #F9FAFB | Screen backgrounds |
| Surface | #FFFFFF | Cards, dialogs |
| Text Primary | #111827 | Headings, body text |
| Text Secondary | #6B7280 | Captions, hints |
| Border | #E5E7EB | Dividers, card borders |

### Typography

| Style | Font | Size | Weight | Usage |
|-------|------|------|--------|-------|
| Headline Large | System | 28sp | Bold (700) | Screen titles |
| Headline Medium | System | 24sp | SemiBold (600) | Section headers |
| Title Large | System | 20sp | SemiBold (600) | Card titles |
| Title Medium | System | 16sp | Medium (500) | List item titles |
| Body Large | System | 16sp | Regular (400) | Primary body text |
| Body Medium | System | 14sp | Regular (400) | Secondary body text |
| Label Large | System | 14sp | Medium (500) | Button text |
| Caption | System | 12sp | Regular (400) | Timestamps, hints |

### Spacing System (8pt Grid)

| Token | Value | Usage |
|-------|-------|-------|
| xs | 4px | Icon padding, tight spacing |
| sm | 8px | Between related elements |
| md | 16px | Card padding, section gaps |
| lg | 24px | Between sections |
| xl | 32px | Screen padding top/bottom |
| xxl | 48px | Major section separators |

---

### Screen: Login Page (SMS OTP)

**Route:** `/login`
**Access:** Public (no auth required)

**Layout:**
```
┌────────────────────────────────────┐
│ [Safe Area Top]                    │
├────────────────────────────────────┤
│                                    │
│         [App Logo 120x120]         │
│                                    │
│       Инспекция квартир            │
│    Проверка при заселении          │
│                                    │
│  ┌────────────────────────────────┐│
│  │ 📞 +7 (___) ___-__-__         ││
│  │ [Введите номер телефона]        ││
│  └────────────────────────────────┘│
│                                    │
│  ┌────────────────────────────────┐│
│  │   Получить код                 ││
│  └────────────────────────────────┘│
│                                    │
│  ┌────────────────────────────────┐│
│  │  _  _  _  _  _  _             ││
│  │  [Введите код из SMS]          ││
│  └────────────────────────────────┘│
│                                    │
│   Повторный код через 01:30        │
│   Отправить повторно                │
│                                    │
├────────────────────────────────────┤
│ [Safe Area Bottom]                 │
└────────────────────────────────────┘
```

**UI Elements:**

| Element | Type | Properties | States |
|---------|------|------------|--------|
| SafeArea | Container | Top/Bottom safe area padding | Default |
| Logo | Image/Icon | 120x120px, centered, primary color (#2563EB) | Default |
| Title | Text | Headline Large (28sp), Text Primary, centered | Default |
| Subtitle | Text | Body Medium (14sp), Text Secondary, centered | Default |
| CountryCodeSelector | CountryCodePicker | +7 default (Russia), tap to change | Default, Expanded |
| PhoneField | TextField | Hint "Введите номер телефона", keyboardType phone | Default, Focused, Error |
| PhoneIcon | Icon | Icons.phone, Primary color, 24px | Default |
| SendCodeButton | ElevatedButton | Full width - 32px margins, 56px height, Primary color | Default, Pressed, Disabled, Loading |
| SendCodeText | Text | Label Large (14sp), white | Default |
| LoadingSpinner | CircularProgressIndicator | 24px, white | Loading |
| OTPDotsRow | Row | 6 individual digit containers with underline | Default, Filled, Error |
| OTPDigit | Container | 48x56px, underline style, centered text | Empty, Filled, Error |
| OTPDigitText | Text | Headline Medium (24sp), Bold | Default |
| OTPDigitField | TextField | Hidden, 6 characters max, keyboard number | Default, Focused |
| ResendTimer | Text | Body Medium (14sp), Text Secondary | Counting, Expired |
| ResendButton | TextButton | "Отправить повторно", Primary color | Enabled, Disabled |
| ErrorText | Text | Body Medium (14sp), Error color (#EF4444) | Default |
| SuccessSnackbar | SnackBar | Secondary color (#10B981), white text | Success |
| ErrorSnackbar | SnackBar | Error color, white text, 4 second duration | Error |

**Login Flow States:**

1. **Phone Entry State:**
   - Show phone input field
   - SendCodeButton enabled when phone has 10+ digits
   - On send: validate phone, call /auth/request-otp, transition to OTP state

2. **OTP Entry State:**
   - Show 6-digit OTP input
   - Auto-submit when 6 digits entered
   - Call /auth/verify-otp
   - On success: store token, navigate to Home
   - On error: show error, clear OTP, allow retry

3. **Resend Flow:**
   - Show countdown timer (60 seconds)
   - After timer expires: show "Отправить повторно" button
   - On resend: call /auth/request-otp again, reset timer

**Validation:**
- Phone: Required, must match pattern +7XXXXXXXXXX (10 digits after country code)
- OTP: Required, exactly 6 digits

**Behaviors:**
- Enter phone: Real-time validation, format as (XXX) XXX-XX-XX
- Tap country code: Show country picker modal
- Tap Send Code: Validate phone, show loading, send OTP via API
- OTP auto-focus: After entering 6th digit, automatically verify
- Tap OTP digit: Show keyboard, focus that position
- Backspace: Remove digit, move focus left
- Paste OTP: Fill all 6 digits if valid length
- Timer expires: Enable resend button with animation
- Tap resend: Call API, reset timer, show success snackbar
- Error: Shake animation on OTP fields, show error message

**SMS Configuration:**
- OTP code: 6 random digits
- Expiry: 5 minutes
- Max attempts: 3 per code
- Rate limit: 3 requests per minute per phone number

---

### Screen: Home / Inspection List

**Route:** `/` (Tab 1)  
**Access:** Authenticated

**Layout:**
```
┌────────────────────────────────────┐
│ [App Bar]                          │
│   Инспекции              [+ Add]  │
├────────────────────────────────────┤
│ [Search Bar]                       │
│ [🔍 Поиск по адресу...]           │
├────────────────────────────────────┤
│ [Filter Chips]                     │
│ [Все] [Заселение] [Выселение]      │
├────────────────────────────────────┤
│ ┌────────────────────────────────┐ │
│ │ 🏠 Москва, Пресненская наб.   │ │
│ │    Заселение • 15 июн          │ │
│ │    [✓ Готов]                   │ │
│ │    3 комнаты • 12 фото         │ │
│ └────────────────────────────────┘ │
│ ┌────────────────────────────────┐ │
│ │ 🏠 Москва, ул. Арбат, 5       │ │
│ │    Выселение • 20 июн          │ │
│ │    [📝 Черновик]               │ │
│ │    2 комнаты • 5 фото          │ │
│ └────────────────────────────────┘ │
│                                    │
│         [Floating Action Button]    │
│              [+ Осмотр]             │
│                                    │
├────────────────────────────────────┤
│ [Bottom Navigation Bar]             │
│ [Осмотры] [Карта] [Отчёты] [Профиль]│
└────────────────────────────────────┘
```

**UI Elements:**

| Element | Type | Properties | States |
|---------|------|------------|--------|
| AppBar | AppBar | Title "Инспекции", elevation 0, white background | Default, Scrolled (shadow appears) |
| AddButton | IconButton | Icons.add, Primary color, 48x48 touch target | Default, Pressed |
| SearchBar | TextField | 48px height, Surface background, 8px border radius, search icon prefix | Default, Focused, With text |
| SearchIcon | Icon | Icons.search, Text Secondary color | Default |
| SearchHint | Text | "Поиск по адресу...", Text Secondary color | Default |
| FilterChipRow | SingleChildScrollView | Horizontal scroll, 8px padding | Default |
| FilterChip | ChoiceChip | Label Medium (14sp), 32px height, 16px horizontal padding | Selected, Unselected |
| FilterChipIcon | Icon | Icons.check, 16px, white when selected | Selected |
| InspectionListView | ListView.builder | padding 16px, pull-to-refresh enabled | Default, Empty, Loading |
| InspectionCard | Card | 100px min height, Surface background, 12px border radius, 8px elevation | Default, Pressed |
| AddressText | Text | Title Medium (16sp), Text Primary, max 2 lines, ellipsis overflow | Default |
| TypeBadge | Container | 6px vertical padding, 8px horizontal padding, colored background | Move_in (green), Move_out (orange) |
| TypeBadgeText | Text | Caption (12sp), white color | Default |
| DateText | Text | Body Medium (14sp), Text Secondary | Default |
| StatusBadge | Container | Row of Icon (12sp) + Text (Caption) | Draft (gray), Completed (green), Sent (blue) |
| StatsRow | Row | Icons.meeting_room (16sp) + count + 16px gap + Icons.photo (16sp) + count | Default |
| FAB | FloatingActionButton | 56px diameter, Primary color, elevation 6 | Default, Pressed |
| FABIcon | Icon | Icons.add, white, 24px | Default |
| BottomNavBar | BottomNavigationBar | 4 items, type fixed, Primary color selected | Default |
| NavItem | BottomNavigationBarItem | Icon + Label, 48px touch target | Default, Selected |
| EmptyIllustration | Column | Icon (64px), Title + Subtitle centered | Default |
| EmptyIcon | Icon | Icons.assignment_outlined, 64px, Text Secondary | Default |
| EmptyTitle | Text | Title Medium, Text Primary | Default |
| EmptySubtitle | Text | Body Medium, Text Secondary | Default |
| SkeletonCard | Shimmer | 100px height, matches InspectionCard structure | Loading |
| ErrorBanner | Column | Icon + message + button | Error |
| RetryButton | TextButton | "Повторить", Primary color | Default, Pressed |

**Behaviors:**
- Pull to refresh: Trigger RefreshIndicator, reload inspections from API
- Tap card: Ripple effect, navigate to Inspection Detail with Hero animation on address
- Long press card: Show context menu (Edit, Delete, Duplicate)
- Tap FAB: Navigate to Create Inspection with slide-up transition
- Tap search: Focus TextField, show keyboard, filter list as user types (debounced 300ms)
- Tap filter chip: Immediately filter list, deselect others
- Tap nav item: Switch tabs with crossfade animation
- Scroll list: AppBar title becomes "Инспекции (N)" when scrolled

---

### Screen: Create Inspection

**Route:** `/inspections/create`  
**Access:** Authenticated

**Layout:**
```
┌────────────────────────────────────┐
│ [App Bar]                          │
│   ← Новый осмотр     [Готово]      │
├────────────────────────────────────┤
│                                    │
│ ┌────────────────────────────────┐ │
│ │ 📍 Адрес                       │ │
│ │ [Введите адрес или укажите     │ │
│ │  на карте...]                  │ │
│ │                                │ │
│ │ [📍 Определить местоположение] │ │
│ └────────────────────────────────┘ │
│                                    │
│ ┌────────────────────────────────┐ │
│ │ 📅 Дата осмотра                │ │
│ │ [Выберите дату]                │ │
│ └────────────────────────────────┘ │
│                                    │
│ ┌────────────────────────────────┐ │
│ │ 🔄 Тип осмотра                 │ │
│ │ [Заселение    ▼]               │ │
│ │  ○ Заселение                   │ │
│ │  ● Выселение                   │ │
│ └────────────────────────────────┘ │
│                                    │
│ ┌────────────────────────────────┐ │
│ │ 📝 Заметки (опционально)       │ │
│ │ [Добавьте заметки...]          │ │
│ └────────────────────────────────┘ │
│                                    │
└────────────────────────────────────┘
```

**UI Elements:**

| Element | Type | Properties | States |
|---------|------|------------|--------|
| AppBar | AppBar | Back arrow, title "Новый осмотр", elevation 0 | Default |
| BackButton | IconButton | Icons.arrow_back, 48x48 | Default, Pressed |
| TitleText | Text | Title Large (20sp), Text Primary | Default |
| SaveButton | TextButton | "Готово", Primary color, disabled state | Enabled, Disabled, Loading |
| Form | Column | ScrollView with 16px padding | Default, Scrolled |
| AddressCard | Card | Surface background, 12px radius, 16px padding | Default, Focused, Error |
| AddressIcon | Icon | Icons.location_on, Primary color, 24px | Default |
| AddressLabel | Text | Caption (12sp), Text Secondary | Default, Error |
| AddressField | TextField | Expand 1, hint text, no border | Default, Focused, Error |
| AddressHint | Text | "Введите адрес или укажите на карте", Text Secondary | Default |
| LocationButton | OutlinedButton | Full width, Icons.my_location, "Определить местоположение" | Default, Loading, Disabled |
| DateCard | Card | Surface background, 12px radius, 16px padding | Default, Tapped |
| DateIcon | Icon | Icons.calendar_today, Primary color, 24px | Default |
| DateLabel | Text | Caption (12sp), Text Secondary | Default |
| DateField | InkWell | "Выберите дату" or selected date | Default, Tapped |
| DateHint | Text | "Выберите дату", Text Secondary when empty | Default, Selected |
| TypeCard | Card | Surface background, 12px radius, 16px padding | Default, Expanded |
| TypeIcon | Icon | Icons.swap_horiz, Primary color, 24px | Default |
| TypeLabel | Text | Caption (12sp), Text Secondary | Default |
| TypeSelector | DropdownButtonFormField | Default value "Заселение", underline none | Default, Expanded, Error |
| TypeOption | DropdownMenuItem | "Заселение" or "Выселение" | Selected, Unselected |
| NotesCard | Card | Surface background, 12px radius, 16px padding | Default, Focused |
| NotesIcon | Icon | Icons.notes, Primary color, 24px | Default |
| NotesLabel | Text | Caption (12sp), Text Secondary | Default |
| NotesField | TextField | Expand 3, hint text, max 500 chars, counter text | Default, Focused, Error |
| NotesHint | Text | "Добавьте заметки...", Text Secondary | Default |
| CharCounter | Text | "0/500", Caption, Text Secondary | Default, Warning (450+), Error (500) |
| DiscardDialog | AlertDialog | Title + message + 2 buttons | Default |
| DiscardTitle | Text | Title Medium, Text Primary | Default |
| DiscardMessage | Text | Body Medium, Text Secondary | Default |
| DiscardCancel | TextButton | "Отмена", Text Secondary | Default, Pressed |
| DiscardConfirm | TextButton | "Выйти", Error color | Default, Pressed |
| LoadingOverlay | Stack | Semi-transparent black + CircularProgressIndicator | Loading |

**Validation Behaviors:**
- Address validation: Real-time, show error after 500ms debounce if < 5 chars
- Date validation: Prevent past dates in picker, show error if not selected on submit
- Type validation: Default always valid, no user input needed
- Form dirty state: Track all changes, enable discard dialog on any modification

**Behaviors:**
- Tap location button: Show loading state, request GPS permission if needed, get coordinates, call reverse geocode API, populate address field
- Tap date field: Open native iOS/Android date picker modal
- Select type dropdown: Update state immediately with animation
- Type in notes: Update char counter, prevent exceeding 500 chars
- Tap save: Validate all fields, show loading overlay, create inspection via API, navigate back on success
- Back navigation with dirty form: Show discard confirmation dialog
- Confirm discard: Navigate back without saving

---

### Screen: Inspection Detail

**Route:** `/inspections/{id}`  
**Access:** Authenticated

**Layout:**
```
┌────────────────────────────────────┐
│ [App Bar]                          │
│   ← [⋮ Menu]           [📄 PDF]   │
├────────────────────────────────────┤
│ ┌────────────────────────────────┐ │
│ │ 📍 Москва, Пресненская наб. 12 │ │
│ │    Заселение • 15 июня 2026    │ │
│ │    [✓ Готов]                   │ │
│ └────────────────────────────────┘ │
├────────────────────────────────────┤
│ Комнаты (3)              [+ Добавить]│
├────────────────────────────────────┤
│ ┌────────────────────────────────┐ │
│ │ 🚪 Гостиная                    │ │
│ │    3 фото • Хорошее состояние  │ │
│ │    [Thumbnail Grid 3x1]        │ │
│ └────────────────────────────────┘ │
│ ┌────────────────────────────────┐ │
│ │ 🚪 Кухня                       │ │
│ │    2 фото • Есть повреждения   │ │
│ │    [Thumbnail Grid 2x1]        │ │
│ └────────────────────────────────┘ │
│ ┌────────────────────────────────┐ │
│ │ 🚪 Спальня                     │ │
│ │    0 фото • Не осмотрено       │ │
│ │    [+ Добавить фото]           │ │
│ └────────────────────────────────┘ │
├────────────────────────────────────┤
│ [Action Buttons Row]               │
│ [📷 Фото] [📍 Карта] [📤 Отправить]│
├────────────────────────────────────┤
│ [Bottom Navigation Bar]             │
└────────────────────────────────────┘
```

**UI Elements:**

| Element | Type | Properties | States |
|---------|------|------------|--------|
| AppBar | AppBar | Back button, menu dots, PDF action button | Default, Scrolled |
| BackButton | IconButton | Icons.arrow_back, 48x48 | Default |
| MenuButton | PopupMenuButton | Icons.more_vert, 48x48 | Default |
| MenuItem | PopupMenuItem | Icon + label, destructive style for delete | Default, Pressed |
| PDFButton | IconButton | Icons.picture_as_pdf, Primary color, 48x48 | Default, Loading |
| HeaderCard | Card | Same structure as InspectionCard in list | Default, Loading |
| AddressRow | Row | Icon + Text | Default |
| AddressText | Text | Title Medium, Text Primary | Default |
| TypeDateRow | Row | TypeBadge + " • " + DateText | Default |
| StatusBadge | Container | Icon + text, colored by status | Draft, Completed, Sent |
| RoomSection | Column | 16px top padding | Default, Empty |
| RoomSectionHeader | Row | "Комнаты (N)" title + Add button | Default |
| RoomCountText | Text | Title Medium (16sp), Text Primary | Default |
| AddRoomButton | TextButton | "+ Добавить", Primary color | Default, Pressed |
| RoomList | ListView.builder | 8px vertical padding, 16px horizontal | Default, Empty |
| RoomCard | Card | 100px min height, Surface background, 12px radius, 4px elevation | Default, Pressed |
| RoomIcon | Icon | Icons.meeting_room, Primary color, 24px | Default |
| RoomInfo | Column | Room name, stats row, condition summary | Default |
| RoomName | Text | Title Medium, Text Primary | Default |
| PhotoStats | Row | Camera icon + count | Default |
| ConditionSummary | Text | Body Medium, Text Secondary, truncated | Default |
| ThumbnailGrid | Row | Max 3 images, 48x48, 4px border radius | Default, Empty |
| Thumbnail | ClipRRect | 48x48, border radius 4px, BoxFit.cover | Default |
| AddPhotoCTA | Container | "+ Добавить" with dashed border | Default |
| ActionButtonsRow | Row | 3 buttons, 8px gap, 16px padding | Default |
| ActionButton | OutlinedButton | Icon + label, 48px height | Default, Pressed, Disabled |
| AddRoomBottomSheet | ModalBottomSheet | 80% max height, rounded top corners | Default |
| BottomSheetHandle | Container | 40px width, 4px height, gray, centered | Default |
| RoomNameField | TextField | Hint "Название комнаты", border enabled | Default, Focused, Error |
| RoomNameSuggestions | ListView | Predefined room names | Default |
| DeleteDialog | AlertDialog | Warning icon, title, message, 2 buttons | Default |
| DeleteButton | TextButton | "Удалить", Error color | Default, Pressed |
| PDFLoadingDialog | AlertDialog | CircularProgressIndicator + "Создание PDF..." | Loading |
| SendOptionsSheet | ModalBottomSheet | ListTile options for email/telegram | Default |
| SendOption | ListTile | Icon + title + subtitle | Default, Pressed |
| RetryButton | ElevatedButton | "Повторить", Primary color | Default, Pressed |
| EmptyRoomsIllustration | Column | Icon + text + button | Default |
| EmptyIcon | Icon | Icons.meeting_room_outlined, 64px, Text Secondary | Default |
| EmptyText | Text | Body Large, Text Secondary | Default |

**Menu Options:**
| Option | Icon | Behavior |
|--------|------|----------|
| Редактировать | Icons.edit | Navigate to edit mode |
| Дублировать | Icons.copy | Create copy of inspection |
| Удалить | Icons.delete | Show delete confirmation dialog |

**Room Card Behaviors:**
- Tap: Ripple effect, navigate to Room Detail with Hero animation on room name
- Long press: Show context menu
- Swipe left: Reveal delete action button

**Action Button Behaviors:**
- Фото: Navigate to camera/gallery picker
- Карта: Show location on map (placeholder)
- Отправить: Show send options bottom sheet

**Empty State Behaviors:**
- Show when inspection has 0 rooms
- Tap "Добавьте первую комнату": Open add room bottom sheet

---

### Screen: Room Detail

**Route:** `/rooms/{id}`  
**Access:** Authenticated

**Layout:**
```
┌────────────────────────────────────┐
│ [App Bar]                          │
│   ← Гостиная          [✏️ Edit]    │
├────────────────────────────────────┤
│ ┌────────────────────────────────┐ │
│ │        [Photo Gallery]          │ │
│ │   ┌─────┐ ┌─────┐ ┌─────┐     │ │
│ │   │ 📷1 │ │ 📷2 │ │ + 📷│     │ │
│ │   └─────┘ └─────┘ └─────┘     │ │
│ │   [Page Indicator ● ○ ○]       │ │
│ └────────────────────────────────┘ │
├────────────────────────────────────┤
│ Состояние элементов                │
├────────────────────────────────────┤
│ ┌────────────────────────────────┐ │
│ │ 🚪 Двери          [Хорошо   ▼] │ │
│ │ 🪟 Окна          [Хорошо   ▼] │ │
│ │ 🧱 Стены          [Требует ▼] │ │
│ │ 🪵 Пол             [Хорошо   ▼]│ │
│ │ 🎨 Потолок        [Хорошо   ▼]│ │
│ └────────────────────────────────┘ │
├────────────────────────────────────┤
│ 📝 Заметки                         │
│ ┌────────────────────────────────┐ │
│ │ Осмотр проведён 15 июня...     │ │
│ └────────────────────────────────┘ │
├────────────────────────────────────┤
│ [Bottom Action]                    │
│ [      📷 Добавить фото      ]    │
└────────────────────────────────────┘
```

**UI Elements:**

| Element | Type | Properties | States |
|---------|------|------------|--------|
| AppBar | AppBar | Back button, edit button, transparent background | Visible, Hidden (tap to toggle) |
| EditButton | IconButton | Icons.edit, white, 48x48 | Default, Edit mode |
| RoomNameText | Text | Title Large (20sp), can be edited | Default, Editing |
| RoomNameField | TextField | Appears when editing, save/cancel actions | Default, Focused |
| PhotoGallery | PageView.builder | Horizontal scroll, full width, 250px height | Default, Empty |
| PhotoPageIndicator | DotsIndicator | PageController, Primary color active dot | Default |
| PhotoThumbnail | GestureDetector | InteractiveViewer for zoom, network image | Default, Loading, Error |
| AddPhotoPlaceholder | Container | "+" icon, dashed border, 250px height | Default |
| AddPhotoIcon | Icon | Icons.add_a_photo, 48px, Text Secondary | Default |
| ElementsSection | Column | 16px padding | Default |
| SectionTitle | Text | Title Medium (16sp), Text Secondary, 12px bottom margin | Default |
| ElementsList | ListView.separated | 8px vertical padding | Default |
| ElementTile | ListTile | Icon + name + dropdown, 56px height | Default |
| ElementIcon | Icon | Varies by element type, 24px, Primary color | Default |
| ElementName | Text | Body Large (16sp), Text Primary | Default |
| ElementDropdown | DropdownButton | Current condition value | Default, Expanded |
| ConditionOption | DropdownMenuItem | Text + colored indicator dot | Selected, Unselected |
| GoodIndicator | Container | 8px circle, green (#10B981) | Default |
| NeedsRepairIndicator | Container | 8px circle, yellow (#F59E0B) | Default |
| NotInspectedIndicator | Container | 8px circle, gray (#6B7280) | Default |
| NotesSection | Column | 16px padding | Default |
| NotesHeader | Row | Icon + title + expand icon | Default, Expanded |
| NotesIcon | Icon | Icons.notes, Primary color | Default |
| NotesTitle | Text | Body Large, Text Primary | Default |
| ExpandIcon | Icon | Icons.expand_more, rotates 180° when expanded | Collapsed, Expanded |
| NotesText | Text | Body Medium, Text Secondary, max 3 lines collapsed | Default, Expanded |
| NotesField | TextField | Appears when expanded, multi-line | Default, Focused |
| AddPhotoButton | ElevatedButton | Full width, camera icon, "Добавить фото" | Default, Pressed, Disabled |
| CameraIcon | Icon | Icons.camera_alt, 20px, white | Default |
| ButtonText | Text | Label Large (14sp), white | Default |
| PhotoDeleteDialog | AlertDialog | "Удалить фото?", confirmation buttons | Default |
| SourcePickerSheet | ModalBottomSheet | Camera and Gallery options | Default |
| SourceOption | ListTile | Icon + "Камера" or "Галерея" | Default, Pressed |
| ElementAutoSaveSnackbar | SnackBar | "Сохранено", 2 second duration, undo action | Success |
| UploadProgressIndicator | LinearProgressIndicator | Primary color, shows during photo upload | Loading |

**Element Types and Icons:**

| Element | Icon | Default State |
|---------|------|---------------|
| Двери (Doors) | Icons.door_front_door | Не осмотрено |
| Окна (Windows) | Icons.window | Не осмотрено |
| Стены (Walls) | Icons.wallpaper | Не осмотрено |
| Пол (Floor) | Icons.square_foot | Не осмотрено |
| Потолок (Ceiling) | Icons.home | Не осмотрено |

**Condition Dropdown Options:**

| Condition | Color | Auto-save |
|----------|-------|-----------|
| Хорошо | Green (#10B981) | Yes (immediate) |
| Требует ремонта | Yellow (#F59E0B) | Yes (immediate) |
| Не осмотрено | Gray (#6B7280) | Yes (immediate) |

**Behaviors:**
- Tap photo: Navigate to full screen viewer with Hero animation
- Long press photo: Show delete confirmation dialog
- Swipe gallery: Animate between photos, update page indicator
- Select dropdown: Immediately save to API, show success snackbar with undo
- Expand notes: Animate expand/collapse, load notes field when expanded
- Tap add photo: Show source picker (camera/gallery)
- Take photo: Compress, get GPS, upload with progress indicator
- Edit room name: Transform text to editable field with save/cancel

---

### Screen: Photo Viewer (Full Screen)

**Route:** `/photos/{id}/view`  
**Access:** Authenticated

**Layout:**
```
┌────────────────────────────────────┐
│ [Status Bar - Light]               │
├────────────────────────────────────┤
│ [←] [⋮]                    [🗑️]  │
├────────────────────────────────────┤
│                                    │
│                                    │
│         [Zoomable Image]           │
│                                    │
│                                    │
│                                    │
├────────────────────────────────────┤
│ 📍 GPS: 55.7495, 37.5375          │
│ 📅 15 июн 2026, 14:30             │
│ 📝 Фото гостиной                   │
└────────────────────────────────────┘
```

**UI Elements:**

| Element | Type | Properties | States |
|---------|------|------------|--------|
| StatusBar | StatusBar | Light content, transparent | Default |
| TransparentAppBar | AppBar | Transparent, white icons, no elevation | Visible, Hidden |
| BackButton | IconButton | Icons.arrow_back, white, 48x48 | Default, Pressed |
| MenuButton | IconButton | Icons.more_vert, white, 48x48 | Default, Pressed |
| DeleteButton | IconButton | Icons.delete_outline, white, 48x48 | Default, Pressed |
| PhotoView | InteractiveViewer | Min scale 1.0, max scale 4.0, ClipRect | Default |
| PhotoImage | Image | Full screen, BoxFit.contain | Loading, Loaded, Error |
| LoadingIndicator | CircularProgressIndicator | Centered, white, 48px | Loading |
| ErrorIcon | Icon | Icons.broken_image, white, 64px | Error |
| MetadataBar | Container | Black gradient background, safe area padding | Default, Hidden |
| MetadataContent | Column | Photo metadata: GPS, date, description | Default |
| GPSRow | Row | Location icon + coordinates or address | Default |
| GPSIcon | Icon | Icons.location_on, white, 16px | Default |
| GPSText | Text | Caption (12sp), white, 70% opacity | Default |
| DateRow | Row | Calendar icon + formatted date/time | Default |
| DateIcon | Icon | Icons.calendar_today, white, 16px | Default |
| DateText | Text | Caption (12sp), white, 70% opacity | Default |
| DescriptionRow | Row | If description exists | Default |
| DescriptionText | Text | Caption (12sp), white, 70% opacity | Default |
| ShareSheet | BottomSheet | Native share options | Default |
| DeleteDialog | AlertDialog | Warning title, message, cancel/delete buttons | Default |
| PhotoActionsMenu | PopupMenuButton | View, Share, Delete options | Default |

**InteractiveViewer Behaviors:**
- Single tap: Toggle app bar and metadata visibility (300ms animation)
- Double tap: Toggle between 1x and 2x zoom
- Pinch: Continuous zoom from 1x to 4x
- Pan: Move zoomed image within viewport
- Fling: Momentum scrolling when zoomed out

**Navigation Between Photos:**
- Swipe left: Navigate to next photo (if available)
- Swipe right: Navigate to previous photo (if available)
- Update PageView controller and metadata bar

**Menu Options:**
| Option | Icon | Behavior |
|--------|------|----------|
| Поделиться | Icons.share | Open system share sheet |
| Удалить | Icons.delete | Show delete confirmation |

---

### Screen: Profile

**Route:** `/profile` (Tab 4)  
**Access:** Authenticated

**Layout:**
```
┌────────────────────────────────────┐
│ [App Bar]                          │
│   Профиль                          │
├────────────────────────────────────┤
│         ┌────────┐                 │
│         │ [Avatar]│                │
│         │   64px │                 │
│         └────────┘                 │
│       Иван Петров                  │
│    📧 ivan@telegram.user           │
├────────────────────────────────────┤
│ 📊 Статистика                      │
│ ┌────────────────────────────────┐ │
│ │ Всего осмотров        12       │ │
│ │ За этот месяц          3       │ │
│ │ В этом году           15       │ │
│ └────────────────────────────────┘ │
├────────────────────────────────────┤
│ 📦 Тарифный план                   │
│ ┌────────────────────────────────┐ │
│ │ ⭐ PRO                          │ │
│ │ • Безлимитные осмотры          │ │
│ │ • PDF отчёты                   │ │
│ │ • Приоритетная поддержка       │ │
│ │                                │ │
│ │ [🔄 Сменить план]              │ │
│ └────────────────────────────────┘ │
├────────────────────────────────────┤
│ ⚙️ Настройки                       │
│ ┌────────────────────────────────┐ │
│ │ 🔔 Уведомления           [ON]  │ │
│ │ 🌙 Тёмная тема           [OFF] │ │
│ │ 📱 О приложении                │ │
│ │ 🚪 Выйти                       │ │
│ └────────────────────────────────┘ │
└────────────────────────────────────┘
```

**UI Elements:**

| Element | Type | Properties | States |
|---------|------|------------|--------|
| AppBar | AppBar | Title "Профиль", elevation 0 | Default |
| ProfileHeader | Column | Centered, 32px top padding | Default |
| AvatarContainer | Stack | 80x80 container | Default |
| Avatar | CircleAvatar | 64px diameter, Primary background, initials or photo | Default, Loading |
| AvatarInitials | Text | "ИП" (initials), Title Large, white | Default |
| AvatarPhoto | Image | BoxFit.cover, 64px CircleAvatar | Loaded |
| CameraButton | Positioned | 24px icon, bottom-right of avatar | Default, Pressed |
| CameraIcon | Container | 24px, Primary color, white icon | Default, Pressed |
| NameText | Text | Headline Medium (24sp), Text Primary, centered | Default |
| EmailText | Text | Body Medium (14sp), Text Secondary, centered | Default |
| StatsCard | Card | 16px padding, 12px border radius, Surface background | Default |
| StatsGrid | Row | 3 columns with dividers | Default |
| StatItem | Column | Value + label, centered | Default |
| StatValue | Text | Headline Medium (24sp), Text Primary, Bold | Default |
| StatLabel | Text | Caption (12sp), Text Secondary | Default |
| StatDivider | VerticalDivider | 1px width, Border color | Default |
| PlanCard | Card | Gradient background (Primary to Primary Dark), 16px padding | Default |
| PlanIcon | Icon | Icons.star, white, 32px | Default |
| PlanName | Text | Headline Medium (24sp), white, Bold | Default |
| FeatureList | Column | Checkmark items | Default |
| FeatureItem | Row | Icon + text | Default |
| FeatureIcon | Icon | Icons.check, green, 16px | Default |
| FeatureText | Text | Body Medium (14sp), white, 80% opacity | Default |
| ChangePlanButton | OutlinedButton | White border, white text, "Сменить план" | Default, Pressed |
| SettingsSection | Column | 24px top padding | Default |
| SectionTitle | Text | Title Medium (16sp), Text Secondary | Default |
| SettingsList | ListView.separated | 8px vertical padding | Default |
| SettingsTile | ListTile | Icon + title + trailing widget | Default, Pressed |
| SettingsIcon | Icon | Varies by setting, Primary color, 24px | Default |
| SettingsTitle | Text | Body Large (16sp), Text Primary | Default |
| SwitchTile | SwitchListTile | Settings switch with icon and title | On, Off |
| NavigationTile | ListTile | Arrow right icon | Default |
| NotificationSwitch | Switch | Controlled by bloc | On, Off |
| DarkModeSwitch | Switch | Controlled by bloc | On, Off |
| AboutTile | ListTile | Info icon, "О приложении" | Default, Pressed |
| LogoutTile | ListTile | Exit icon, "Выйти", Error color | Default, Pressed |
| LogoutDialog | AlertDialog | Warning title, message, cancel/logout buttons | Default |
| ChangeAvatarSheet | ModalBottomSheet | Camera/Gallery options | Default |
| AppInfoDialog | AlertDialog | App version, build number | Default |

**Settings Tiles Configuration:**

| Setting | Icon | Trailing | Behavior |
|---------|------|----------|----------|
| Уведомления | Icons.notifications | Switch | Toggle notifications |
| Тёмная тема | Icons.dark_mode | Switch | Toggle dark mode |
| О приложении | Icons.info_outline | None | Show app info dialog |
| Выйти | Icons.exit_to_app | None | Show logout confirmation |

**Plan Features by Tier:**

| Tier | Features |
|------|----------|
| Free | 1 inspection/month, Basic support |
| Starter | 5 inspections/month, PDF reports |
| Pro | Unlimited inspections, Priority support |
| Premium | Unlimited + API access + White label |

**Behaviors:**
- Tap avatar: Show change avatar bottom sheet with camera/gallery options
- Tap camera button: Same as avatar tap
- Change avatar: Open camera/gallery, compress, upload with progress
- Tap stats: Show detailed stats modal (future)
- Tap plan card: Navigate to plan selection/pricing page
- Toggle notification switch: Update preference, show snackbar confirmation
- Toggle dark mode: Immediately apply theme, persist preference
- Tap about: Show app info dialog with version
- Tap logout: Show confirmation dialog
- Confirm logout: Clear auth token, navigate to login with fade transition

---

### Screen: Map View (Future Feature Placeholder)

**Route:** `/map` (Tab 2)  
**Access:** Authenticated

**Layout:**
```
┌────────────────────────────────────┐
│ [App Bar]                         │
│   Карта                          │
├────────────────────────────────────┤
│                                    │
│         [Map Placeholder]         │
│                                    │
│    🏠 🏠                          │
│         🏠                        │
│                                    │
│                                    │
│ ┌────────────────────────────────┐ │
│ │ В разработке                   │ │
│ │ Эта функция будет доступна    │ │
│ │ в следующей версии            │ │
│ └────────────────────────────────┘ │
│                                    │
└────────────────────────────────────┘
```

**Note:** This is a placeholder screen for future map integration feature.

---

### Screen: Reports (PDF List)

**Route:** `/reports` (Tab 3)  
**Access:** Authenticated

**Layout:**
```
┌────────────────────────────────────┐
│ [App Bar]                          │
│   Отчёты                          │
├────────────────────────────────────┤
│ ┌────────────────────────────────┐ │
│ │ 📄 Отчёт #12                   │ │
│ │ Москва, Пресненская 12          │ │
│ │ 📅 15 июн 2026 • 2.4 MB        │ │
│ │ [📥 Скачать] [📤 Поделиться]   │ │
│ └────────────────────────────────┘ │
│ ┌────────────────────────────────┐ │
│ │ 📄 Отчёт #8                    │ │
│ │ Москва, Арбат 5                 │ │
│ │ 📅 10 июн 2026 • 1.8 MB        │ │
│ │ [📥 Скачать] [📤 Поделиться]   │ │
│ └────────────────────────────────┘ │
│                                    │
└────────────────────────────────────┘
```

**UI Elements:**

| Element | Type | Properties | States |
|---------|------|------------|--------|
| AppBar | AppBar | Title "Карта", elevation 0 | Default |
| MapPlaceholder | Container | Full screen, gray background (#F3F4F6) | Default |
| PlaceholderIcon | Icon | Icons.map_outlined, 120px, Text Secondary | Default |
| PlaceholderText | Column | "В разработке" + description | Default |
| PlaceholderTitle | Text | Title Large, Text Secondary | Default |
| PlaceholderDescription | Text | Body Medium, Text Secondary, centered | Default |
| PlaceholderCard | Card | Surface background, 16px padding, centered | Default |

**Note:** This is a placeholder screen. Full implementation planned for Phase 3.

---

### Screen: Reports (PDF List)

**UI Elements:**

| Element | Type | Properties | States |
|---------|------|------------|--------|
| AppBar | AppBar | Title "Отчёты", elevation 0 | Default |
| ReportsList | ListView.builder | 16px padding | Default, Empty, Loading |
| ReportCard | Card | Surface background, 12px radius, 8px elevation | Default, Pressed |
| ReportIcon | Icon | Icons.picture_as_pdf, Error color (#EF4444), 40px | Default |
| ReportInfo | Column | Inspection address, date + file size | Default |
| ReportAddress | Text | Title Medium (16sp), Text Primary, max 1 line | Default |
| ReportMeta | Row | Date + " • " + file size | Default |
| ReportDate | Text | Body Medium (14sp), Text Secondary | Default |
| FileSize | Text | Body Medium (14sp), Text Secondary | Default |
| ReportActions | Row | Download + Share buttons | Default |
| DownloadButton | TextButton | Icons.download + "Скачать", Primary color | Default, Loading, Pressed |
| ShareButton | TextButton | Icons.share + "Поделиться", Primary color | Default, Pressed |
| LoadingIndicator | SizedBox | 20px CircularProgressIndicator | Loading |
| EmptyIllustration | Column | Icon + text centered | Default |
| EmptyIcon | Icon | Icons.description_outlined, 64px, Text Secondary | Default |
| EmptyTitle | Text | Title Medium, Text Primary | Default |
| EmptySubtitle | Text | Body Medium, Text Secondary | Default |
| SkeletonCard | Shimmer | 80px height, matches ReportCard structure | Loading |
| PDFViewerPage | Page | Full screen PDF preview | Default, Loading, Error |
| PDFViewer | PdfView | File path or URL, page by page | Loading, Loaded, Error |
| PDFLoadingIndicator | CircularProgressIndicator | Centered, 48px | Loading |
| PDFErrorWidget | Column | Error icon + message + retry | Error |
| ShareSheet | BottomSheet | Native share options | Default |

**Behaviors:**
- Tap report card: Open PDF viewer with Hero animation on icon
- Tap download: Download PDF to device, show progress, save to downloads folder
- Tap share: Open system share sheet with PDF file
- Pull to refresh: Reload reports list from API
- Swipe left on card: Reveal delete action
- Tap retry on error: Reload PDF

---

## 6. Mobile-Specific Considerations

### Offline Support

1. **Local Database:** Use sqflite for local storage
2. **Sync Strategy:**
   - Queue mutations when offline
   - Sync when connection restored
   - Show sync status indicator
3. **Data to Cache:**
   - User profile
   - Recent inspections (last 10)
   - Draft inspections

### Camera Integration

1. **Image Compression:** Resize to max 1920px width before upload
2. **EXIF Data:** Preserve GPS coordinates from camera
3. **Gallery Access:** Allow selecting existing photos
4. **Progress Upload:** Show upload progress for large photos

### UX Best Practices

1. **One-Handed Use:** Keep important actions in bottom half of screen
2. **Haptic Feedback:** Use light vibration on button presses
3. **Loading States:** Never block UI for more than 2 seconds
4. **Error Messages:** Show actionable error messages, not technical details
5. **Pull to Refresh:** Available on all list screens
6. **Swipe Actions:** Swipe to delete on list items

---

## 7. State Management (BLoC)

### AuthBloc

**Events:**
- RequestOtpRequested(phone)
- VerifyOtpRequested(phone, code)
- LogoutRequested
- CheckAuthStatus

**States:**
- AuthInitial
- PhoneEntryState
- OtpSentState(phone)
- OtpVerifyingState
- AuthLoading
- Authenticated(user)
- Unauthenticated
- OtpError(message, remainingAttempts)
- AuthError(message)

**OTP Flow State Machine:**

```
PhoneEntryState
    │
    ▼ (RequestOtp)
OtpSentState ─────── (error) ──────▶ OtpError
    │                                   │
    ▼ (VerifyOtp)                       │
OtpVerifyingState                       │
    │                                   │
    ├──── (success) ──▶ Authenticated ──┘
    │
    └──── (fail) ──────▶ OtpError
```

**OTP Error Handling:**
- Invalid code: Show "Неверный код", remaining attempts decrease
- Expired code: Show "Код истёк", prompt to resend
- Max attempts: Show "Превышено число попыток", wait 5 minutes
- Rate limited: Show "Попробуйте через X секунд"

### InspectionBloc

**Events:**
- LoadInspections
- CreateInspection(data)
- UpdateInspection(id, data)
- DeleteInspection(id)
- RefreshInspections

**States:**
- InspectionInitial
- InspectionLoading
- InspectionsLoaded(inspections)
- InspectionOperationSuccess(message)
- InspectionError(message)

### RoomBloc

**Events:**
- LoadRooms(inspectionId)
- AddRoom(inspectionId, data)
- UpdateRoom(id, data)
- DeleteRoom(id)
- AddPhoto(roomId, file)

**States:**
- RoomInitial
- RoomLoading
- RoomsLoaded(rooms)
- RoomOperationSuccess
- RoomError(message)

---

## 8. API Error Handling

### Error Response Format

```json
{
  "message": "Validation failed",
  "errors": {
    "address": ["The address field is required."]
  }
}
```

### Error Codes

| HTTP Code | Handling |
|-----------|----------|
| 400 | Show validation errors from response |
| 401 | Navigate to login, clear token |
| 403 | Show "Access denied" message |
| 404 | Show "Not found" with option to go back |
| 422 | Show field-specific validation errors |
| 500 | Show generic error with retry option |
| Network Error | Show "No internet connection" banner |

---

## 9. Implementation Priorities

### Phase 1 (MVP)
1. Login via SMS OTP
2. Inspection CRUD
3. Room management
4. Photo upload
5. Basic profile

### Phase 2
1. PDF generation and viewing
2. Offline support
3. Push notifications

### Phase 3
1. Map integration
2. Real-time sync
3. Advanced analytics