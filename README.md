# Custom Block Styles

A lightweight, reusable WordPress class for registering custom block styles with associated stylesheets.

## Features

- Automatically registers and enqueues block style CSS files
- Simple array-based configuration
- Works with any core or custom block
- Built-in file existence checking
- Cache busting via `filemtime()`
- WPCS compliant

## Installation

1. Copy the `custom-block-styles` folder to your theme directory
2. Include the class file in your `functions.php`:

```php
require_once get_template_directory() . '/custom-block-styles/class-custom-block-styles.php';
```

## Usage

### Basic Example

```php
// Define your custom block styles
$custom_block_styles = array(
    array(
        'block' => 'core/image',
        'name'  => 'rounded-corners',
        'label' => 'Rounded Corners',
    ),
    array(
        'block' => 'core/group',
        'name'  => 'card-style',
        'label' => 'Card Style',
    ),
);

// Initialize the class
new Custom_Block_Styles( $custom_block_styles, '/assets/css/styles/' );
```

### File Structure

The class expects CSS files to be named after the `name` value:

```
your-theme/
├── custom-block-styles/
│   └── class-custom-block-styles.php
└── assets/
    └── css/
        └── styles/
            ├── rounded-corners.css
            └── card-style.css
```

### CSS File Example

**`rounded-corners.css`:**
```css
.wp-block-image.is-style-rounded-corners img {
    border-radius: 12px;
}
```

The class automatically prefixes your style name with `is-style-`.

## Configuration

### Array Keys

Each block style array requires three keys:

| Key | Type | Description |
|-----|------|-------------|
| `block` | string | Block type (e.g., `core/image`, `core/group`) |
| `name` | string | Style slug (used for CSS handle and filename) |
| `label` | string | Human-readable label shown in the editor |

### Custom CSS Path

By default, CSS files are loaded from `/assets/css/styles/`. You can change this:

```php
new Custom_Block_Styles( $custom_block_styles, '/css/block-styles/' );
```

## Requirements

- WordPress 5.9+
- PHP 7.0+

## License

GPL-2.0+

## Changelog

### 1.0.0
- Initial release

