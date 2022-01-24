# PHPFusion CLI (Discontinued)

**This was experimental project**

![Visitor Badge](https://visitor-badge.laobi.icu/badge?page_id=PF-Projects.PF-CLI)

## Installation

### Source

Add `path\to\PF-CLI\bin` to your system PATH

### .phar file

Download pf.phar

Check the Phar file to verify that it's working

```
C:\path>php pf.phar -V
```

Adding Phar to PATH

**Windows**

Create pf\pf.bat and paste this code. pf.phar must be in the same folder. Then you can add script to your PATH

```shell
@ECHO OFF
php "%~dp0pf.phar" %*
```

## Usage

```
C:\path>pf command [arguments] [options]
```

## Commands

### `info` - Show server info

Arguments:

- No arguments

Options:

- No options

### `generate:theme` - Generate site theme

Arguments:

- `name` string (Required) Theme name

Options:

- `--author` or `-a` string (Optional) Author name
- `--website` or `-w` string (Optional) Website
- `--oop` or `-o` bool (Optional) Set this option you want OOP theme
- `--activate` or `-A` bool (Optional) Set this option to enable theme
- `--license` or `-l` string (Optional) License type (none|agpl|epal)

### `generate:admintheme` - Generate admin theme

Arguments:

- `name` string (Required) Admin theme name

Options:

- `--author` or `-a` string (Optional) Author name
- `--oop` or `-o` bool (Optional) Set this option you want OOP admin theme
- `--activate` or `-A` bool (Optional) Set this option to enable admin theme
- `--license` or `-l` string (Optional) License type (none|agpl|epal)

### `generate:infusion` - Generate infusion

Arguments:

- `name` string (Required) Infusion name

Options:

- `--author` or `-a` string(Optional) Author name
- `--email` or `-e` string (Optional) Author email
- `--website` or `-w` string (Optional) Website
- `--rights` or `-r` string (Optional) Admin Rights for infusions. E.g. XX. Max. 4 characters. If it's empty, a random
  string is generated
- `--activate` or `-A` bool (Optional) Set this option to enable infusion
- `--license` or `-l` string (Optional) License type (none|agpl|epal)

### `generate:panel` - Generate panel

Arguments:

- `name` string (Required) Panel name

Options:

- `--author` or `-a` string (Optional) Author name
- `--activate` or `-A` bool (Optional) Set this option to enable panel
- `--license` or `-l` string (Optional) License type (none|agpl|epal)

### `generate:page` Generate php page

Arguments:

- `name` string (Required) Page name

Options:

- `--author` or `-a` string (Optional) Author name
- `--license` or `-l` string (Optional) License type (none|agpl|epal)

### `core:requirements` - Checks system requirements

Arguments:

- No arguments

Options:

- No options

### `core:install` - Install PHPFusion

W.I.P

## Requirements

- PHPFusion 9.10.00 or newer
- PHP +7.2.5
