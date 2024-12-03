# Security Policy

## Supported Versions

The following versions of the project are supported with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |

## PHP Compatibility

The project supports the following PHP versions:

- PHP `^7.4`  
- PHP `^8.0` and above

It is recommended to use the latest stable PHP version for optimal security and performance.

## Dependencies

The project relies on the following dependencies:

- **Parsedown** (`erusev/parsedown`): `^1.7`  
  For parsing and rendering Markdown.
- **Symfony YAML Component** (`symfony/yaml`): `^7.2`  
  For working with YAML files.
- **Symfony Filesystem Component** (`symfony/filesystem`): `^7.2`  
  For filesystem operations.
- **Symfony Dependency Injection Component** (`symfony/dependency-injection`): `^7.2`  
  For dependency injection and service management.
- **PSR Container** (`psr/container`): `^2.0`  
  Provides a common interface for dependency injection containers.
- **PSR Log** (`psr/log`): `^3.0`  
  Standardizes logging interfaces.

### Development Dependencies

- **PHP_CodeSniffer** (`squizlabs/php_codesniffer`): `^3.7`  
  Used for enforcing coding standards in development.

Keep dependencies up to date to ensure the security of your project.

## Reporting a Vulnerability

To report a vulnerability:

1. Open an issue on the [GitHub Issues](https://github.com/KodoHub/UchuDocs/issues) page.
2. Use the "Security" label to tag your issue.

### What to Include:
- A detailed description of the vulnerability.
- Steps to reproduce the issue.
- Potential impact and any known mitigations.

### Response Expectations:
- You will receive a response within **5 business days**.
- Valid reports will be acknowledged, and updates will be provided on the resolution timeline.
