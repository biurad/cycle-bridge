# The Biurad PHP Bridge For Cycle ORM

[![Latest Version](https://img.shields.io/packagist/v/biurad/cycle-bridge.svg?style=flat-square)](https://packagist.org/packages/biurad/cycle-bridge)
[![Software License](https://img.shields.io/badge/License-BSD--3-brightgreen.svg?style=flat-square)](LICENSE)
[![Workflow Status](https://img.shields.io/github/workflow/status/biurad/cycle-bridge/Tests?style=flat-square)](https://github.com/biurad/cycle-bridge/actions?query=workflow%3ATests)
[![Code Maintainability](https://img.shields.io/codeclimate/maintainability/biurad/cycle-bridge?style=flat-square)](https://codeclimate.com/github/biurad/cycle-bridge)
[![Coverage Status](https://img.shields.io/codecov/c/github/biurad/cycle-bridge?style=flat-square)](https://codecov.io/gh/biurad/cycle-bridge)
[![Quality Score](https://img.shields.io/scrutinizer/g/biurad/cycle-bridge.svg?style=flat-square)](https://scrutinizer-ci.com/g/biurad/cycle-bridge)
[![Sponsor development of this project](https://img.shields.io/badge/sponsor%20this%20package-%E2%9D%A4-ff69b4.svg?style=flat-square)](https://biurad.com/sponsor)

**biurad/cycle-bridge** is a Well-integrated Cycle ORM for [PHP] 7.2+ Nette and Biurad Framework created by [Divine Niiquaye][@divineniiquaye]. This library allows Cycle ORM and Spiral Database to be used with [Biurad Dependency Injection][biurad-di].

## 📦 Installation & Basic Usage

This project requires [PHP] 7.2 or higher. The recommended way to install, is via [Composer]. Simply run:

```bash
$ composer require biurad/cycle-bridge
```

## 📓 Documentation

For in-depth documentation before using this library. Full documentation on advanced usage, configuration, and customization can be found at [cycle-orm.dev][docs].

## ⏫ Upgrading

Information on how to upgrade to newer versions of this library can be found in the [UPGRADE].

## 🏷️ Changelog

[SemVer](http://semver.org/) is followed closely. Minor and patch releases should not introduce breaking changes to the codebase; See [CHANGELOG] for more information on what has changed recently.

Any classes or methods marked `@internal` are not intended for use outside of this library and are subject to breaking changes at any time, so please avoid using them.

## 🛠️ Maintenance & Support

When a new **major** version is released (`1.0`, `2.0`, etc), the previous one (`0.19.x`) will receive bug fixes for _at least_ 3 months and security updates for 6 months after that new release comes out.

(This policy may change in the future and exceptions may be made on a case-by-case basis.)

**Professional support, including notification of new releases and security updates, is available at [Biurad Commits][commit].**

## 👷‍♀️ Contributing

To report a security vulnerability, please use the [Biurad Security](https://security.biurad.com). We will coordinate the fix and eventually commit the solution in this project.

Contributions to this library are **welcome**, especially ones that:

- Improve usability or flexibility without compromising our ability to adhere to ???.
- Optimize performance
- Fix issues with adhering to ???.
- ???.

Please see [CONTRIBUTING] for additional details.

## 🧪 Testing

```bash
$ composer test
```

This will tests biurad/cycle-bridge will run against PHP 7.2 version or higher.

## 👥 Credits & Acknowledgements

- [Divine Niiquaye Ibok][@divineniiquaye]
- [All Contributors][]

## 🙌 Sponsors

Are you interested in sponsoring development of this project? Reach out and support us on [Patreon](https://www.patreon.com/biurad) or see <https://biurad.com/sponsor> for a list of ways to contribute.

## 📄 License

**biurad/cycle-bridge** is licensed under the BSD-3 license. See the [`LICENSE`](LICENSE) file for more details.

## 🏛️ Governance

This project is primarily maintained by [Divine Niiquaye Ibok][@divineniiquaye]. Members of the [Biurad Lap][] Leadership Team may occasionally assist with some of these duties.

## 🗺️ Who Uses It?

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us an [email] or [message] mentioning this library. We publish all received request's at <https://patreons.biurad.com>.

Check out the other cool things people are doing with `biurad/cycle-bridge`: <https://packagist.org/packages/biurad/cycle-bridge/dependents>

[PHP]: https://php.net
[Composer]: https://getcomposer.org
[@divineniiquaye]: https://github.com/divineniiquaye
[docs]: https://cycle-orm.dev/docs
[commit]: https://commits.biurad.com/cycle-bridge.git
[UPGRADE]: UPGRADE-1.x.md
[CHANGELOG]: CHANGELOG-0.x.md
[CONTRIBUTING]: ./.github/CONTRIBUTING.md
[All Contributors]: https://github.com/biurad/cycle-bridge/contributors
[Biurad Lap]: https://team.biurad.com
[email]: support@biurad.com
[message]: https://projects.biurad.com/message
[biurad-di]: https://github.com/biurad/php-dependency-injection
