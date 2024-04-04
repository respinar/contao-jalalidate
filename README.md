# Iranian Date Bundle for Contao Open Source CMS

[![Latest Stable Version](https://poser.pugx.org/respinar/contao-jalalidate/v/stable.svg)](https://packagist.org/packages/respinar/contao-jalalidate) [![Total Downloads](https://poser.pugx.org/respinar/contao-jalalidate/downloads.svg)](https://packagist.org/packages/respinar/contao-jalalidate) [![Latest Unstable Version](https://poser.pugx.org/respinar/contao-jalalidate/v/unstable.svg)](https://packagist.org/packages/respinar/contao-jalalidate) [![License](https://poser.pugx.org/respinar/contao-jalalidate/license.svg)](https://packagist.org/packages/respinar/contao-jalalidate)

This is the Iranian Date bundle for Contao. After installing with Composer, the Use Iranian Date setting in the root page configuration allows you to toggle the Solar Hijri date format. If enabled, all dates will be displayed in the Solar Hijri (Iranian) format site-wide, regardless of the language. If the setting is disabled or for other languages, the Georgian (Gregorian) date will be displayed.

## New Feature: Iranian Date Toggle

- **Added `Use Iranian Date` setting** to root pages in Contao 5.3+.
- Enable this setting to apply Iranian date formatting site-wide, regardless of the language.
- Translations now use the XLIFF format with support for English (`en`), Persian (`fa`), and German (`de`).

## New Feature: `{{iranian_date}}` Insert Tag

We've introduced a new insert tag: `{{iranian_date}}`. This tag allows you to display the current Iranian (Jalali) date in your Contao templates, and it offers flexibility in terms of date formatting.

### How It Works

The `{{iranian_date}}` insert tag works similarly to Contao's built-in `{{date}}` insert tag. It allows you to display the current date in the Iranian calendar with customizable date formats.

#### Syntax:
```html
{{iranian_date::[format]}}
```

- `format`: The format string for the Iranian date. If no format is provided, the tag will fall back to the global date format set in Contao or use the default format `j F Y` (e.g., 15 Esfand 1403).

#### Example Usage:
1. **Custom Format**:
    ```html
    <p>Current Iranian Date: {{iranian_date::j F Y}}</p>
    ```
    This will display the current date in the format `15 Esfand 1403`.

2. **Fallback to Default Format**:
    ```html
    <p>Current Iranian Date: {{iranian_date}}</p>
    ```
    If no format is provided, it will fall back to the global date format configured in Contao (or `j F Y` if no global format is set).

3. **Custom Timestamp**:
    ```html
    <p>Specific Date: {{iranian_date::j F Y::1617235200}}</p>
    ```
    You can also pass a custom timestamp to display a specific date in Iranian format.

### Features:
- **Customizable Date Format**: You can specify the format for the Iranian date using the standard PHP date format (`d, m, Y, F, j`).
- **Fallback to Contao's Global Date Format**: If no format is provided, it will use the global date format from Contao settings.
- **Timestamp Support**: You can pass a specific timestamp to display a date other than the current date.

### Installation

To install the **Contao Jalali Date Bundle**, run the following Composer command:

```bash
composer require respinar/contao-jalalidate
```

### Configuration

1. **Global Date Format**: You can configure the global date format in your Contao backend. This will be the default format used for the `{{iranian_date}}` tag when no format is provided.
2. **Custom Date Format**: You can specify a custom date format directly in the `{{iranian_date::[format]}}` insert tag.

### License

MIT License. See [LICENSE](LICENSE) for more details.