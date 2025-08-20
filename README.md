# Filament Resource Builder

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ofthewildfire/filament-resource-builder.svg?style=flat-square)](https://packagist.org/packages/ofthewildfire/filament-resource-builder)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ofthewildfire/potential-palm-tree/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ofthewildfire/potential-palm-tree/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ofthewildfire/potential-palm-tree/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ofthewildfire/potential-palm-tree/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ofthewildfire/filament-resource-builder.svg?style=flat-square)](https://packagist.org/packages/ofthewildfire/filament-resource-builder)

A powerful Filament plugin that allows users to create custom resources dynamically through the GUI. Build complete CRUD interfaces without writing code!

## Features

- 🚀 **Dynamic Resource Creation** - Create resources through Filament's GUI
- 📝 **Multiple Field Types** - Text, number, email, checkbox, date, textarea, select
- 🗄️ **Automatic Database Tables** - Creates real database tables automatically
- 🔄 **Live Preview** - See your resources in navigation immediately
- 🎯 **No Code Required** - Perfect for non-developers
- 🧩 **Filament Native** - Integrates seamlessly with existing Filament apps

## Installation

You can install the package via composer:

```bash
composer require ofthewildfire/filament-resource-builder
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-resource-builder-migrations"
php artisan migrate
```

## Setup

Add the plugin to your Filament panel in `app/Providers/Filament/AdminPanelProvider.php`:

```php
use Fuascailtdev\FilamentResourceBuilder\FilamentResourceBuilderPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other configuration
        ->plugins([
            FilamentResourceBuilderPlugin::make(),
        ]);
}
```

## Usage

1. **Access the Resource Builder**: Navigate to "Resource Builder" in your Filament admin panel
2. **Create a New Resource**: Click "Create" and enter your resource details (e.g., "Products")
3. **Add Fields**: Use the repeater to add fields like:
   - Product Title (text)
   - Price (number)
   - Description (textarea)
   - Is Featured (checkbox)
4. **Save**: Your resource will automatically appear in the navigation
5. **Manage Data**: Click on your new resource to start adding and managing data

## Example

Creating a "Products" resource with title, price, and description fields will:
- Create a `products` database table
- Generate a complete Filament resource with forms and tables
- Add "Products" to your navigation menu
- Allow full CRUD operations on product data

## Supported Field Types

- **Text** - Single line text input
- **Textarea** - Multi-line text input
- **Number** - Numeric input
- **Email** - Email input with validation
- **Password** - Password input
- **Select** - Dropdown with custom options
- **Checkbox** - Boolean checkbox
- **Date** - Date picker
- **DateTime** - Date and time picker

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [fuascailtdev](https://github.com/ofthewildfire)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.