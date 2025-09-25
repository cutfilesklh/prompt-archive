# AI Prompt Archive

A comprehensive WordPress plugin for managing and organizing AI prompts with workspaces, categories, and advanced search capabilities.

## Features

- 📚 **Multiple Workspaces** - Organize prompts into different workspaces
- 🏷️ **Categories & Tags** - Full taxonomy support
- 🔍 **Advanced Search** - Filter by category, model, difficulty
- 📊 **Analytics** - Track prompt usage and popularity
- 🎨 **Customizable Design** - Multiple themes and layouts
- 📥 **Import/Export** - CSV and JSON support
- ⚡ **Fast & Lightweight** - Optimized for performance

## Installation

### From GitHub

1. Clone this repository or download as ZIP:
```bash
git clone https://github.com/yourusername/prompt-archive.git
```

2. Upload to your WordPress plugins directory:
```bash
/wp-content/plugins/prompt-archive/
```

3. Activate the plugin through WordPress admin

### Manual Installation

1. Download the latest release
2. Upload via WordPress Admin → Plugins → Add New → Upload Plugin
3. Activate the plugin

## Quick Start

1. After activation, go to **AI Prompts** in your WordPress admin
2. Create your first workspace: **AI Prompts → Workspaces**
3. Add your first prompt: **AI Prompts → Add New**
4. Display prompts using shortcode: `[ai_prompt_library]`

## Shortcodes

### Main Library
```
[ai_prompt_library workspace="marketing" columns="3" limit="12"]
```

### Grid Display
```
[ai_prompt_grid category="content-creation" show_filters="yes"]
```

### Search Box
```
[ai_prompt_search placeholder="Search prompts..."]
```

## Configuration

Navigate to **AI Prompts → Settings** to configure:

- Default view (Grid/List/Compact)
- Prompts per page
- Color schemes
- Enable/disable features
- API integrations

## Development

### Requirements
- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+

### Hooks & Filters

```php
// Modify prompt display
add_filter('aipl_prompt_content', 'your_function');

// Add custom fields
add_action('aipl_after_prompt_meta', 'your_function');

// Customize search
add_filter('aipl_search_args', 'your_function');
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

GPL v2 or later - see [LICENSE](LICENSE) file for details

## Support

- 📖 [Documentation](https://github.com/yourusername/prompt-archive/wiki)
- 🐛 [Report Issues](https://github.com/yourusername/prompt-archive/issues)
- 💬 [Discussions](https://github.com/yourusername/prompt-archive/discussions)

## Credits

Created by [Your Name](https://yourwebsite.com)
