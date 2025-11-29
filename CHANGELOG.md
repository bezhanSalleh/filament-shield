# Changelog

All notable changes to `filament-shield` will be documented in this file.

## 4.0.3 - 2025-11-29

### What's Changed

#### Release Summary Highlights

- Unified label resolution with consistent fallback chain:
  - User's translation file (when localization.enabled)
  - Package's resource_permission_prefixes_labels
  - Headline fallback - Fixed getLocalizedLabel() to use correct translation path
  
- `shield:translation` New Command to generate translation/lang file for the provided locale for permissions of the given panel
- Fixes setup on windows
- Fixes multiple guard issues when creating roles/permissions

* fix(stubs): correct custom stub path resolution by @TheSyriableDev in https://github.com/bezhanSalleh/filament-shield/pull/654
* Update README.md by @jeffersongoncalves in https://github.com/bezhanSalleh/filament-shield/pull/657
* Update transform custom permissions to use config case by @ShamarKellman in https://github.com/bezhanSalleh/filament-shield/pull/659
* Fix: ensure selected Panel guard is respected and include guard when creating roles/permissions (fixes #647) by @LuizCristino in https://github.com/bezhanSalleh/filament-shield/pull/660
* Update localization key in filament-shield config by @EG-Mohamed in https://github.com/bezhanSalleh/filament-shield/pull/663
* Bump stefanzweifel/git-auto-commit-action from 6 to 7 by @dependabot[bot] in https://github.com/bezhanSalleh/filament-shield/pull/664
* Update Spanish Translations by @luisprmat in https://github.com/bezhanSalleh/filament-shield/pull/669
* Bump actions/checkout from 5 to 6 by @dependabot[bot] in https://github.com/bezhanSalleh/filament-shield/pull/672
* Streamline permission label resolution system by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/675

### New Contributors

* @TheSyriableDev made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/654
* @jeffersongoncalves made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/657
* @ShamarKellman made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/659
* @LuizCristino made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/660
* @EG-Mohamed made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/663
* @luisprmat made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/669

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/4.0.2...4.0.3

## 4.0.2 - 2025-09-11

### What's Changed

* Includes features and fixes
  - [Feat] Custom Permission Generation
  - [Feat] Single Command to Prohibit Destructive Commands
  - [Fix] `shield:seeder` command
  - [Doc] Documenting:
    - Installation & Setup
    - Permissions and `buildPermissionKeyUsing()` documented with usage and example
    - Policies
    - Resources
    - Pages & Widgets
    - Custom Permissions
    - Plugin & Resource
    - Commands
    - Localization
    
  

By @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/621

* Fix SimplePage #613 by @hamrak in https://github.com/bezhanSalleh/filament-shield/pull/614
* [Fix:Bug:WindowsOS] Resource Specific Permissions by @tszulczewski in https://github.com/bezhanSalleh/filament-shield/pull/625
* [Fix:#629] Edit/Create when tenancy is enabled by @Wsmallnews in https://github.com/bezhanSalleh/filament-shield/pull/630

### New Contributors

* @tszulczewski made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/625
* @Wsmallnews made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/630

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/4.0.1...4.0.2

## gp - 2025-09-08

### What's Changed

* [Fix] Tenancy Part of `shield:setup` command by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/611

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/4.0.0...4.0.1

## 4.0.0 - 2025-09-07

### What's Changed

* Support for v4 stable release by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/592
* Fixes #560 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/572
* Fixes artisan cache/optimize by @malzariey in https://github.com/bezhanSalleh/filament-shield/pull/559
* Add missing CZ and SK translations (4.x) by @hamrak in https://github.com/bezhanSalleh/filament-shield/pull/574
* Bump aglipanci/laravel-pint-action from 2.5 to 2.6 by @dependabot[bot] in https://github.com/bezhanSalleh/filament-shield/pull/601
* Bump stefanzweifel/git-auto-commit-action from 5 to 6 by @dependabot[bot] in https://github.com/bezhanSalleh/filament-shield/pull/602
* Bump actions/checkout from 4 to 5 by @dependabot[bot] in https://github.com/bezhanSalleh/filament-shield/pull/603

### New Contributors

* @malzariey made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/559

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.3.7...4.0.0

## 3.3.7 - 2025-06-11

### What's Changed

* Bump dependabot/fetch-metadata from 2.3.0 to 2.4.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/540
* [Fix] widget permissions on super admin by @Dimas-Saputra-Me in https://github.com/bezhanSalleh/filament-shield/pull/539
* Update Polish translation by @WierzbickiKrzysztof in https://github.com/bezhanSalleh/filament-shield/pull/532
* Switch Negated Ternary in RoleResource by @alisalehi1380 in https://github.com/bezhanSalleh/filament-shield/pull/542
* Feat: Improve custom permissions for third party packages. by @carloeusebi in https://github.com/bezhanSalleh/filament-shield/pull/547
* [Fix] allow Resource to be part of the Filament Resource name by @haugen86 in https://github.com/bezhanSalleh/filament-shield/pull/549
* [Fix] Commands for auto setup/installtion to be cross-os compatible by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/552

### New Contributors

* @Dimas-Saputra-Me made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/539
* @WierzbickiKrzysztof made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/532
* @alisalehi1380 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/542
* @carloeusebi made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/547
* @haugen86 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/549

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.3.6...3.3.7

## 3.3.6 - 2025-05-03

### What's Changed

* [bug] Add optional Tenant to Role name unique rule by @Cyrille37 in https://github.com/bezhanSalleh/filament-shield/pull/517
* refactor: Use dynamic policy namespace for RolePolicy registration by @arthurpar06 in https://github.com/bezhanSalleh/filament-shield/pull/518
* Normalize filpath generation for cross-OS compatibility by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/523
* Fix example methods on Pages Hooks docs by @eduardr10 in https://github.com/bezhanSalleh/filament-shield/pull/527
* Added support for customizabile sub navigation position by @panservicesas in https://github.com/bezhanSalleh/filament-shield/pull/528
* Fixes!!! by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/535

### New Contributors

* @Cyrille37 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/517
* @arthurpar06 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/518
* @eduardr10 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/527
* @panservicesas made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/528

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.3.5...3.3.6

## 3.3.5 - 2025-02-17

### What's Changed

* Bump dependabot/fetch-metadata from 2.2.0 to 2.3.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/497
* Bump aglipanci/laravel-pint-action from 2.4 to 2.5 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/498
* [Fix] Handles Widget Permission Labels through `$heading` by @mahdimirhendi in https://github.com/bezhanSalleh/filament-shield/pull/473
* [Fix] Remove double-pluralization from `getAllResourcePermissions` by @dissto in https://github.com/bezhanSalleh/filament-shield/pull/465
* wire up checkbox column and column span config for resource by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/507
* [Fix] tenant id type by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/508

### New Contributors

* @mahdimirhendi made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/473
* @dissto made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/465

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.3.4...3.3.5

## 3.3.4 - 2024-11-14

### What's Changed

* [Fix] Tenant selection visibility by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/446

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.3.3...3.3.4

## 3.3.3 - 2024-11-10

### What's Changed

* fix: Add error handling for team options when teams are not used by @wout-o in https://github.com/bezhanSalleh/filament-shield/pull/444

### New Contributors

* @wout-o made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/444

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.3.2...3.3.3

## 3.3.2 - 2024-11-10

### What's Changed

* added the ability to use the config defined foreign key for teams feature when enabled.
* fixed the issue when creating/updating a role when tenancy is not enabled.

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.3.1...3.3.2

## 3.3.1 - 2024-11-09

**What's new in 3.3.1?**

* Added the ability to make destructive commands(listed below) `prohibitable`
  - Setup
  - Install
  - Generate
  - Publish
  

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.3.0...3.3.1

## 3.3.0 - 2024-11-09

### What's Changed

* Feature/tenancy by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/441
* Update HasPageShield.php by @naingwin in https://github.com/bezhanSalleh/filament-shield/pull/440
* add Georgian Translations by @kirkita in https://github.com/bezhanSalleh/filament-shield/pull/436
* Rename filament_shield.php to filament-shield.php by @SebastianRajca in https://github.com/bezhanSalleh/filament-shield/pull/432
* Turkish Language update by @enessvg in https://github.com/bezhanSalleh/filament-shield/pull/431
* Updated language ID (Indonesia) by @adereksisusanto in https://github.com/bezhanSalleh/filament-shield/pull/429
* removes console.log() on toggle in form by @Adysone in https://github.com/bezhanSalleh/filament-shield/pull/417
* fix: remove blank row by @giacomomasseron in https://github.com/bezhanSalleh/filament-shield/pull/420

### New Contributors

* @giacomomasseron made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/420
* @Adysone made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/417
* @adereksisusanto made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/429
* @enessvg made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/431
* @SebastianRajca made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/432
* @kirkita made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/436
* @naingwin made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/440

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.2.6...3.3.0

## 3.2.6 - 2024-09-02

### What's Changed

* Bump dependabot/fetch-metadata from 2.1.0 to 2.2.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/405
* Slovak translation by @hamrak in https://github.com/bezhanSalleh/filament-shield/pull/384
* Update Doc for Laravel 11 (Role Policy and Custom folder structure) by @paulhenry46 in https://github.com/bezhanSalleh/filament-shield/pull/387
* Added (sq) Albanian language by @dajanvulaj in https://github.com/bezhanSalleh/filament-shield/pull/389
* Update README.md by @bilogic in https://github.com/bezhanSalleh/filament-shield/pull/391
* Add CKB & KU translation by @RawanD201 in https://github.com/bezhanSalleh/filament-shield/pull/404
* handles role policy auto registration and resolves #407 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/411

### New Contributors

* @hamrak made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/384
* @paulhenry46 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/387
* @dajanvulaj made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/389
* @bilogic made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/391
* @RawanD201 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/404

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.2.5...3.2.6

## 3.2.5 - 2024-05-14

### What's Changed

* [Fix] Not generate `super_admin` role when disabled by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/381
* [Fix] Cluster Page by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/380
* feat: add Polish translation by @AngryDragonite in https://github.com/bezhanSalleh/filament-shield/pull/374
* Fixed Tab bug in chinese language by @curder in https://github.com/bezhanSalleh/filament-shield/pull/362
* 🌐 km translations by @valpuia in https://github.com/bezhanSalleh/filament-shield/pull/356

### New Contributors

* @valpuia made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/356
* @curder made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/362
* @AngryDragonite made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/374

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.2.4...3.2.5

## 3.2.4 - 2024-03-28

### What's Changed

* `Create/Edit` performance boost
* Simple view for Resource Permissions
* `cluster` config
* View permissions as is or by label using the `localizePermissionLabels(condition: false)` method

> [!IMPORTANT]
If you've previously published `RoleResource`, please republish it using `shield:publish` and when prompted; select `yes`.
**Note:** Custom modifications to `RoleResource` may be overwritten. Ensure to manually handle any customizations after republishing by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/336

* Configure the namespace of the policy file by @igwen6w in https://github.com/bezhanSalleh/filament-shield/pull/313
* Make cluster configurable on resource by @pelmered in https://github.com/bezhanSalleh/filament-shield/pull/328
* Add option to shield:seeder command to only generate direct permissions by @Jacobtims in https://github.com/bezhanSalleh/filament-shield/pull/329
* Remove unnecessary PHPDoc by @iRaziul in https://github.com/bezhanSalleh/filament-shield/pull/335
* Don't use hardcoded primary key name by @danswiser in https://github.com/bezhanSalleh/filament-shield/pull/338
* Add translation for LV by @webmasterlv in https://github.com/bezhanSalleh/filament-shield/pull/340
* Overide canAccess that will point to canView by @sbc640964 in https://github.com/bezhanSalleh/filament-shield/pull/341
* Bump ramsey/composer-install from 2 to 3 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/343
* Fix French translations by @invaders-xx in https://github.com/bezhanSalleh/filament-shield/pull/348
* Korean translations add by @corean in https://github.com/bezhanSalleh/filament-shield/pull/352
* Bump dependabot/fetch-metadata from 1.6.0 to 2.0.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/353

### New Contributors

* @igwen6w made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/313
* @pelmered made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/328
* @Jacobtims made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/329
* @iRaziul made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/335
* @danswiser made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/338
* @webmasterlv made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/340
* @sbc640964 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/341
* @invaders-xx made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/348
* @corean made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/352

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.2.3...3.2.4

## 3.2.1 - 2024-01-25

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.2.0...3.2.1

## 3.2.0 - 2024-01-24

### What's Changed

* Feature/performance boost by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/319
  
  > Filament `v3.2`
  Republish `RoleResource`
  
* Update filament-shield.php by @noxoua in https://github.com/bezhanSalleh/filament-shield/pull/312
  
* Add traditional chinese translation by @cssf998811 in https://github.com/bezhanSalleh/filament-shield/pull/321
  

### New Contributors

* @noxoua made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/312
* @cssf998811 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/321

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.1.3...3.2.0

## 3.1.3 - 2024-01-12

### What's Changed

* Proper handling of `WidgetConfiguration` and some improvments by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/310
* Bump aglipanci/laravel-pint-action from 2.3.0 to 2.3.1 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/303
* Renamed folder name to match Filaments i18n structure by @Corvisier in https://github.com/bezhanSalleh/filament-shield/pull/300

### New Contributors

* @Corvisier made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/300

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.1.2...3.1.3

## 3.1.2 - 2023-12-19

### What's Changed

* [fix] Third-party `Widget` plugins issue by @nicko170 in https://github.com/bezhanSalleh/filament-shield/pull/284
* [fix] Updating `ShieldSeeder` stub by @torosegon in https://github.com/bezhanSalleh/filament-shield/pull/290
* [feature] Make policy directory configurable by @lhilton in https://github.com/bezhanSalleh/filament-shield/pull/291
* [fix] `App\Policies\Role` doesn't exists by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/293

### New Contributors

* @nicko170 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/284
* @lhilton made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/291

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.1.1...3.1.2

## 3.1.1 - 2023-12-06

### What's Changed

* Support User model inheritance and configuration of the `HasRoles` trait in the parent model by @coolsam726 in https://github.com/bezhanSalleh/filament-shield/pull/281

### New Contributors

* @coolsam726 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/281

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.1.0...3.1.1

## 3.1.0 - 2023-11-22

### What's Changed

- drop support for spatie permission 5.0 and add support for 6.0 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/280

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.13...3.1.0

## 3.0.13 - 2023-11-22

### What's Changed

- drop support for spatie permission 6.0 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/279

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.12...3.0.13

## 3.0.12 - 2023-11-22

### What's Changed

- feat: adds support for disabling tenant scoping for the permission re… by @djsall in https://github.com/bezhanSalleh/filament-shield/pull/276
- fixes #274 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/277
- Fix/#272 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/278

### New Contributors

- @djsall made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/276

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.11...3.0.12

## 3.0.11 - 2023-11-03

**What's new in 3.0.11?**

- Support for `spatie/laravel-permission` version 6.0

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.10...3.0.11

## 3.0.10 - 2023-11-03

### What's Changed

- Fixed role name uniqueness by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/271
- Fixed widgets localized labels by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/270
- Enhance Arabic Translations in filament-shield.php by @majdghithan in https://github.com/bezhanSalleh/filament-shield/pull/268
- typo in README.md by @majdghithan in https://github.com/bezhanSalleh/filament-shield/pull/267
- Add czech translations by @tomas-doudera in https://github.com/bezhanSalleh/filament-shield/pull/265

### New Contributors

- @tomas-doudera made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/265
- @majdghithan made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/267

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.9...3.0.10

## 3.0.9 - 2023-10-27

**What's new in 3.0.9?**

- Revert to 3.0.4 to fix permission generation for pages and widgets. The issue is user related not package.
  **Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.8...3.0.9

## 3.0.8 - 2023-10-27

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.7...3.0.8

## 3.0.7 - 2023-10-27

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.6...3.0.7

## 3.0.6 - 2023-10-27

### What's Changed

- Fix/permission name by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/261

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.5...3.0.6

## 3.0.5 - 2023-10-27

### What's Changed

- Fix widget and permission name case by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/259
- Add Armenian translation by @ArtMin96 in https://github.com/bezhanSalleh/filament-shield/pull/256
- add $parameters to shouldRegisterNavigation by @rupadana in https://github.com/bezhanSalleh/filament-shield/pull/249
- Update README.md by @fetova in https://github.com/bezhanSalleh/filament-shield/pull/242
- Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/245

### New Contributors

- @fetova made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/242
- @rupadana made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/249
- @ArtMin96 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/256

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.4...3.0.5

## 3.0.4 - 2023-10-02

### What's Changed

- Feature/column grid customizations by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/238
- Update Hungarian translate by @gergo85 in https://github.com/bezhanSalleh/filament-shield/pull/239

### New Contributors

- @gergo85 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/239

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.3...3.0.4

## 3.0.3 - 2023-09-23

### What's Changed

- php exit function replace to return by @mahavishnup in https://github.com/bezhanSalleh/filament-shield/pull/234
- Feature/panel user by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/236

### New Contributors

- @mahavishnup made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/234

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.2...3.0.3

## 3.0.2 - 2023-09-21

### What's Changed

- Introduce new fresh look for Artisan command by @datlechin in https://github.com/bezhanSalleh/filament-shield/pull/233

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.1...3.0.2

## 3.0.1 - 2023-09-16

**what's new**

- made sections collapsible

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/3.0.0...3.0.1

## 2.4.8 - 2023-08-01

### What's Changed

- Bump aglipanci/laravel-pint-action from 2.2.0 to 2.3.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/194
- Bump dependabot/fetch-metadata from 1.4.0 to 1.6.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/203
- Add Group Naming change howto by @tonypartridge in https://github.com/bezhanSalleh/filament-shield/pull/187
- Policies return type by @Frameck in https://github.com/bezhanSalleh/filament-shield/pull/192
- Add lang ja (Japanese) by @shibomb in https://github.com/bezhanSalleh/filament-shield/pull/198

### New Contributors

- @tonypartridge made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/187
- @Frameck made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/192
- @shibomb made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/198

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.4.7...2.4.8

## 2.4.7 - 2023-05-21

**What's new in 2.4.7?**

- fix guard issue while creating and updating role

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.4.6...2.4.7

## 2.4.6 - 2023-05-08

**What's new in 2.4.6**

- fix shield seeder

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.4.5...2.4.6

## 2.4.5 - 2023-05-04

**What's new in 2.4.5?**

- Fixes `ShieldSeeder`

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.4.4...2.4.5

## 2.4.4 - 2023-04-25

**what's changed?**

- [feature] make install command runnable on production environment

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.4.3...2.4.4

## 2.4.3 - 2023-04-25

### What's Changed

- Feature/configure permission identifier by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/186
- chore(documentation): typo by @JohnnyEvo in https://github.com/bezhanSalleh/filament-shield/pull/178
- Update README.md by @chiwex in https://github.com/bezhanSalleh/filament-shield/pull/181
- refactor: make getResourceNavigationSort nullable by @JaZo in https://github.com/bezhanSalleh/filament-shield/pull/182
- Bump aglipanci/laravel-pint-action from 2.1.0 to 2.2.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/180
- Bump dependabot/fetch-metadata from 1.3.6 to 1.4.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/185
- 

### New Contributors

- @JohnnyEvo made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/178
- @chiwex made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/181
- @JaZo made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/182

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.4.2...2.4.3

## 2.4.2 - 2023-02-10

### What's Changed

- Fixes #172 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/176

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.4.1...2.4.2

## 2.4.1 - 2023-02-10

### What's Changed

- Laravel 10 Support by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/174

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.4.0...2.4.1

## 2.4.0 - 2023-02-10

### What's Changed

- feature: publish command by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/173
- Fixed typo in ShieldSeeder.stub by @Jehizkia in https://github.com/bezhanSalleh/filament-shield/pull/150
- Bump ramsey/composer-install from 1 to 2 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/151
- Fix minor typo and grammar by @lioneaglesolutions in https://github.com/bezhanSalleh/filament-shield/pull/156
- Typo in example for custom permission by @bmckay959 in https://github.com/bezhanSalleh/filament-shield/pull/159
- Add DeleteAction in RoleResource by @jvkassi in https://github.com/bezhanSalleh/filament-shield/pull/160
- Bump aglipanci/laravel-pint-action from 1.0.0 to 2.1.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/162
- feat: add hungarian translation by @torosegon in https://github.com/bezhanSalleh/filament-shield/pull/163
- Bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/169
- Complete Arabic translation by @ahmed-abobaker in https://github.com/bezhanSalleh/filament-shield/pull/168
- Fix role resource card columns by @maaz1n in https://github.com/bezhanSalleh/filament-shield/pull/167

### New Contributors

- @Jehizkia made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/150
- @lioneaglesolutions made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/156
- @bmckay959 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/159
- @torosegon made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/163
- @ahmed-abobaker made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/168
- @maaz1n made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/167

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.3.2...2.4.0

## 2.3.2 - 2022-11-16

### What's Changed

- Make Navigation Item visible/hidden by @ThijmenKort in https://github.com/bezhanSalleh/filament-shield/pull/146
- fix:install and super-admin commands when `Model::preventLazyLoading()` by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/147

### New Contributors

- @ThijmenKort made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/146

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.3.1...2.3.2

## 2.3.1 - 2022-11-08

### What's Changed

- Bump dependabot/fetch-metadata from 1.3.4 to 1.3.5 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/141
- feat: use config models by @FurkanGM in https://github.com/bezhanSalleh/filament-shield/pull/139

### New Contributors

- @FurkanGM made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/139

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.3.0...2.3.1

## 2.3.0 - 2022-11-01

### What's Changed

- Feature/liberating resource permissions by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/140

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.2.9...2.3.0

## 2.2.9 - 2022-10-28

### What's Changed

- some optimization courtesy of @SkeyPunyapal

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.2.8...2.2.9

## 2.2.8 - 2022-10-28

### What's Changed

- Bump dependabot/fetch-metadata from 1.3.3 to 1.3.4 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/129
- ro language by @boyfromhell in https://github.com/bezhanSalleh/filament-shield/pull/127
- Ru & UA translation by @HomaEEE in https://github.com/bezhanSalleh/filament-shield/pull/128
- Improve Default Policy Stub Replicate Comment by @intrepidws in https://github.com/bezhanSalleh/filament-shield/pull/130

### New Contributors

- @boyfromhell made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/127
- @HomaEEE made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/128

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.2.7...2.2.8

## 2.2.7 - 2022-09-29

### What's Changed

- vi translations by @datlechin in https://github.com/bezhanSalleh/filament-shield/pull/123
- Feature: Implement optional --minimal flag by @awcodes in https://github.com/bezhanSalleh/filament-shield/pull/124

### New Contributors

- @datlechin made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/123
- @awcodes made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/124

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.2.6...2.2.7

## 2.2.6 - 2022-09-15

### What's Changed

- fixes #120 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/122

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.2.5...2.2.6

## 2.2.5 - 2022-09-11

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.2.4...2.2.5

## 2.2.4 - 2022-09-11

### What's Changed

- adds the ability to set global search status & fixes #118 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/119

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.2.3...2.2.4

## 2.2.3 - 2022-09-03

### What's Changed

- adds `shield:seeder` new command by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/115

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.2.2...2.2.3

## 2.2.2 - 2022-09-03

### What's Changed

- fixes #110 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/112
- Updates filament-shield.php FA translations. by @fsamapoor in https://github.com/bezhanSalleh/filament-shield/pull/111

### New Contributors

- @fsamapoor made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/111

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.2.1...2.2.2

## 2.2.1 - 2022-08-28

### What's Changed

- Fixes #108 auth provider policy generation by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/109

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.2.0...2.2.1

## 2.2.0 - 2022-08-27

### What's Changed

- adds new features to shield by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/107
  
- - adds the ability to define `super-admin` via gage
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - new options for `shield:generate`
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - - --all                    Generate permissions/policies for all entities
    
  
- - 
  
- 
- - 
  
- 
- 
- - 
  
- 
- 
- 
- - 
  
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - - --option[=OPTION]        Override the config generator option(policies_and_permissions,policies,permissions)
    
  
- - 
  
- 
- - 
  
- 
- 
- - 
  
- 
- 
- 
- - 
  
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - - --resource[=RESOURCE]    One or many resources separated by comma (,)
    
  
- - 
  
- 
- - 
  
- 
- 
- - 
  
- 
- 
- 
- - 
  
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - - --page[=PAGE]            One or many pages separated by comma (,)
    
  
- - 
  
- 
- - 
  
- 
- 
- - 
  
- 
- 
- 
- - 
  
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - - --widget[=WIDGET]        One or many widgets separated by comma (,)
    
  
- - 
  
- 
- - 
  
- 
- 
- - 
  
- 
- 
- 
- - 
  
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - - --exclude                Exclude the given entities during generation
    
  
- - 
  
- 
- - 
  
- 
- 
- - 
  
- 
- 
- 
- - 
  
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - - --ignore-config-exclude  Ignore config `exclude` option during generation
    
  
- - 
  
- 
- - 
  
- 
- 
- - 
  
- 
- 
- 
- - 
  
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - new option for `shield:install`
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - - --only            Only setups shield without generating permissions and creating super-admin
    
  
- - 
  
- 
- - 
  
- 
- 
- - 
  
- 
- 
- 
- - 
  
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - 
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - redefined the purpose of `filament_user` role, not attaching permissions anymore
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- Improve Command Section of README by @intrepidws in https://github.com/bezhanSalleh/filament-shield/pull/102
  
- Update README.md by @atmonshi in https://github.com/bezhanSalleh/filament-shield/pull/106
  

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.1.3...2.2.0

## 2.1.3 - 2022-08-09

### What's Changed

- fixes #100 by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/101

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.1.2...2.1.3

## 2.1.2 - 2022-08-08

### What's Changed

- Config option to easily turn off navigation group by @intrepidws in https://github.com/bezhanSalleh/filament-shield/pull/94
- Add flag to `shield:generate` to override generator.option config value by @intrepidws in https://github.com/bezhanSalleh/filament-shield/pull/95
- Fix small typo by @eugenevdm in https://github.com/bezhanSalleh/filament-shield/pull/97

### New Contributors

- @eugenevdm made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/97

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/2.1.1...2.1.2

## 2.1.1 - 2022-08-03

### What's Changed

- fix entity's state @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/commit/6a3008d0a47c9abdea3e0b4abfbbf6d2d50d73f6
- Remove dump by @NathanaelGT in https://github.com/bezhanSalleh/filament-shield/pull/88
- Spanish translation update by @pathros in https://github.com/bezhanSalleh/filament-shield/pull/89
- remove has Views by @atmonshi in https://github.com/bezhanSalleh/filament-shield/pull/90

### New Contributors

- @NathanaelGT made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/88
- @atmonshi made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/90

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v2.1.0...2.1.1

## v2.1.0 - 2022-07-28

### What's Changed

- New `upgrade` command
- Removed `Setting` page
- Added new config key for RoleResource `badge`
- removed extra keys from lang files
- Generate Policies for third-party packages

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v2.0.8...v2.1.0

## v2.0.8 - 2022-07-28

### What's Changed

- New `upgrade` command
- Removed `Setting` page
- Add new config key for RoleResource `badge`
- removed extra keys from lang files
- Allow user option for shield:super-admin command by @intrepidws in https://github.com/bezhanSalleh/filament-shield/pull/85

### New Contributors

- @intrepidws made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/85

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v2.0.7...v2.0.8

## v2.0.7 - 2022-07-28

- fix settings table issue
- **Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v2.0.6...v2.0.7

## v2.0.6 - 2022-07-28

- added the `reorder` policy method and permission_prefix
- fixes the `setting` db check
- **Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v2.0.5...v2.0.6

## v2.0.5 - 2022-07-25

### What's Changed

- Enhancements & improvements by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/69

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v2.0.4...v2.0.5

## v2.0.4 - 2022-07-21

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v2.0.3...v2.0.4

## v2.0.0 - 2022-07-18

### What's Changed

- 2.x by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/61
  
- 
- - Follow filament plugin standards
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - Add Setting Model (DB)
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - Generate config dynamically from setting model
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - Remove Config file
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - Follow new Filament Actions
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - Make default permissions translatable
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - Add ability to Load default settings from DB
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - Remove `shield:publish` command
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- - Remove `RoleResource` stubs
  
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- 
- Italian Translation by @slamservice in https://github.com/bezhanSalleh/filament-shield/pull/50
  
- 
- Bump dependabot/fetch-metadata from 1.3.1 to 1.3.3 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/55
  
- 
- support Windows installation by @hadyfayed in https://github.com/bezhanSalleh/filament-shield/pull/54
  
- 
- add Support for windows installation by @hadyfayed in https://github.com/bezhanSalleh/filament-shield/pull/53
  
- 

### New Contributors

- @slamservice made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/50
- @hadyfayed made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/54

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.12...v2.0.0

## v1.1.12 - 2022-05-19

## What's Changed

- Bump dependabot/fetch-metadata from 1.3.0 to 1.3.1 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/36
- Dutch translations #44 by @sten in https://github.com/bezhanSalleh/filament-shield/pull/45
- add german (de) translations by @simonbuehler in https://github.com/bezhanSalleh/filament-shield/pull/32
- Feature: Uses booted instead of the mount lifecycle method in the HasPageShield trait. by @oyepez003 in https://github.com/bezhanSalleh/filament-shield/pull/43

## New Contributors

- @sten made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/45
- @simonbuehler made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/32
- @oyepez003 made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/43

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.11...v1.1.12

## v1.1.11 - 2022-03-24

## What's Changed

- makes the resources generator option configurable by @bezhanSalleh in https://github.com/bezhanSalleh/filament-shield/pull/31

## New Contributors

- @bezhanSalleh made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/31

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.10...v1.1.11

## v1.1.10 - 2022-03-13

## What's Changed

- fixed settings page authorization
- Bump dependabot/fetch-metadata from 1.2.1 to 1.3.0 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/27
- directory seperator fix by @alperenersoy in https://github.com/bezhanSalleh/filament-shield/pull/26
- Bump actions/checkout from 2 to 3 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/28

## New Contributors

- @alperenersoy made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/26

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.9...v1.1.10

## v1.1.9 - 2022-03-03

- Subs bug fix
- **Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.8...v1.1.9

## v1.1.8 - 2022-02-28

## What's Changed

- Bump dependabot/fetch-metadata from 1.2.0 to 1.2.1 by @dependabot in https://github.com/bezhanSalleh/filament-shield/pull/23
- Enhance policy stubs + Improve Arabic translation by @mohamedsabil83 in https://github.com/bezhanSalleh/filament-shield/pull/20
- added Indonesian translations. by @sayasuhendra in https://github.com/bezhanSalleh/filament-shield/pull/22

## New Contributors

- @sayasuhendra made their first contribution in https://github.com/bezhanSalleh/filament-shield/pull/22

**Full Changelog**: https://github.com/bezhanSalleh/filament-shield/compare/v1.1.7...v1.1.8

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
