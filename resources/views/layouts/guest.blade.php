{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900">
        <div class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 text-gray-500 fill-current" />
                </a>
            </div>

            <div class="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md sm:max-w-md sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html> --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    {{ $slot }}
</body>

</html>
{{-- ```

---

## ✅ **Fitur Login Page Baru:**

### **🎨 Visual:**
- ✅ **Split Screen Design** (50% brand, 50% form)
- ✅ **Gradient Background** (teal modern)
- ✅ **Icon di Input Field** (email & password icon)
- ✅ **Hover Effects** (smooth transitions)
- ✅ **Responsive** (mobile friendly)

### **🚀 UX Improvements:**
- ✅ Input fields dengan icon
- ✅ Placeholder text
- ✅ Focus states yang jelas
- ✅ Error messages lebih visible
- ✅ Remember me checkbox
- ✅ Forgot password link
- ✅ Loading state (bisa ditambah)

### **📱 Responsive:**
```
Desktop (≥1024px):
┌─────────────┬─────────────┐
│             │             │
│   BRAND     │    FORM     │
│   (LEFT)    │   (RIGHT)   │
│             │             │
└─────────────┴─────────────┘

Mobile (<1024px):
┌─────────────┐
│    LOGO     │
│             │
│    FORM     │
│             │
└─────────────┘ --}}
