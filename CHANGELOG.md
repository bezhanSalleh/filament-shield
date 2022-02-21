# Changelog

All notable changes to `filament-shield` will be documented in this file.

## v1.1.7 - 2022-02-21

## What's Changed

- Removed `outlined()`
- Bump dependabot/fetch-metadata from 1.1.1 to 1.2.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/19
- Fixes issue #17 by @brunolipe-a in https://github.com/bezhanSalleh/filament-shield/pull/18
- Add support for Turkish (tr) translation by @trk in https://github.com/bezhanSalleh/filament-shield/pull/16
- [ar] Translate new phrases by @mohamedsabil83 in https://github.com/bezhanSalleh/filament-shield/pull/15
- Add spanish translations by @pathros in https://github.com/bezhanSalleh/filament-shield/pull/14

## New Contributors

- @dependabot made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/19
- @brunolipe-a made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/18
- @trk made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/16
- @pathros made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/14

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.6...v1.1.7

## v1.1.6 - 2022-02-16

## What's Changed

- Add missing trait to upgrade command  by @jvkassi in https://github.com/bezhanSalleh/filament-shield/pull/13

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.5...v1.1.6

## v1.1.5 - 2022-02-16

## What's Changed

- Brand new `Settings` Page 🔥
- Brand new `Config`
- Improved `sheild:install` to detect existing app vs new app install
- `--fresh` flag of `shield:install` now only touches the core package migrations
- Improved `shield:upgrade`
- Backing-up the existing `Config`
- Added the ability to opt-in/out of `super_admin` role
- and much more optimizations...

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.4...v1.1.5

## v1.1.4 - 2022-02-09

## What's Changed

- Laravel 9 Support
- [FR] Translate new phrases by @jvkassi in https://github.com/bezhanSalleh/filament-shield/pull/8

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.3...v1.1.4

## v1.1.3 - 2022-01-10

- Fix `Resource` custom permissions state
- **Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.2...v1.1.3

## v1.1.2 - 2022-01-10

## What's Changed

- Add Brazilian Portuguese by @felipe-balloni in https://github.com/bezhanSalleh/filament-shield/pull/5
- Fix two translations keys by @felipe-balloni in https://github.com/bezhanSalleh/filament-shield/pull/7
- [AR] Translate new phrases by @mohamedsabil83 in https://github.com/bezhanSalleh/filament-shield/pull/6
- Fixed `shield:install` to generate `RolePolicy`

## New Contributors

- @felipe-balloni made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/5

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.0.4...v1.1.2

## v1.1.1 - 2022-01-09

- added `shield:upgrade` command :fire:
- added `Custom Permission` for `Resources` in addition to the default 6
- **Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.0...v1.1.1

## v1.1.0 - 2022-01-09

- Improved permission generation for `Resources` :fire:
- Generate permissions for `Widgets` :fire:
- Generate permissions for `Pages` :fire:
- Show/Hide Permission `Entities` Tab :fire:
- `HasWidgetShield` & `HasPageShield` :fire:
- `Custom` Permissions tab to attache to roles :fire:
- Improved `shield:generate` command
- Improved `except` config permissions generation
- Ability to enable/disable generation for `only` entities listed.
- Ability to exclude `Dashboard`, `AccountWidget` and `FilamentInfoWidget` while generating permissions
- `--all` flag added for `shield:install` command
- `--only` flag added for `shield:install` command

## v1.0.3 - 2022-01-03

- `shield:install` command improved
- installation steps doc updated
- `config` updated
- **Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.0.2...v1.0.3

## 1.0.3 - 2022-01-03

- `shield:install` command improved
- installation steps doc updated
- `config` updated
- `shield:user` renamed to `shield:super-admin`

## 1.0.2 - 2022-01-03

- commands order sorted

## 1.0.1 - 2022-01-03

- public release

## 1.0.0 - 2022-01-03

- initial release
