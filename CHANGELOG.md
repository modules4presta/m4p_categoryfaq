# Changelog

All notable changes to this project are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses
[semantic versioning](https://semver.org/).

## [1.0.0] - 2026-10-27

First open-source release, published under the MIT license.

### Added

- Questions and answers assigned to any number of categories, written per language, ordered by
  position and switched on individually, managed from **Catalog → Category FAQ**.
- `displayM4pCategoryFaq` hook rendering the questions below the product list.
- FAQPage structured data, emitted once per question set on the first unfiltered page of a category.
- LiteSpeed page cache purge of the affected categories when a question is saved.
- en-US and pl-PL catalogues, MIT headers, `LICENSE`, `README`, `SECURITY`, `CONTRIBUTING`,
  `composer.json`, `config.xml` and `index.php` guards in every directory.
