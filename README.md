# Category FAQ for PrestaShop 8 & 9

**Answers the questions customers ask before they buy, on the category page where they ask them, and hands the same answers to search engines as FAQPage data.**

> **Meta description (155 chars):** Questions and answers under the product list of a PrestaShop category, with FAQPage structured data. Free, MIT licensed.

---

## Why a category page needs answers

A category page is where a customer decides what to compare. It lists products and prices and says nothing about the question that actually blocks the purchase: which model fits which job, what the delivery time is, whether the shop issues an invoice for a company. The customer leaves to look for that answer, and often finds it somewhere that also sells the product.

The answers usually exist already – in emails, in chat transcripts, in what the sales team repeats every week. Written once under the product list, they work for every visitor, and as FAQPage data they can be shown directly in search results and quoted by AI assistants.

- **Fewer questions to answer twice** – the answer sits where the question comes up.
- **Text on a page that has none** – a category listing is mostly product names; the questions give it content worth indexing.
- **One source for the page and for search engines** – the same answers render as markup and as JSON-LD.

## What the module does

Questions are managed on their own back-office screen, written per language, assigned to any number of categories and ordered by position. The front office renders them through the `displayM4pCategoryFaq` hook, below the product list, together with FAQPage structured data. It changes nothing in the category itself, in its description or in the product list.

### Key features

- **Questions assigned to categories** – one question can serve several categories; a category can hold any number of questions.
- **Per-language content** – question and answer are translated like any other PrestaShop content.
- **Position and an on/off switch** – lower positions come first; a question can be prepared and shown later.
- **FAQPage structured data** – emitted once per question set, on the first unfiltered page of the category.
- **Page cache purge** – saving a question purges the LiteSpeed cache of the affected categories.

### Paginated, filtered and sorted listings

The questions are rendered on every page of the listing, but the FAQPage data is emitted only on the first page with no search, filter or sorting applied. Repeating the same structured data on page two, on a filtered URL or on a sorted URL would duplicate it, which is what search engines report as an error.

## Compatibility

| | |
|---|---|
| PrestaShop | 8.0 – 9.x |
| PHP | 7.4+ |
| Requirements | none |
| Multistore | Questions are shared by every shop in the installation |
| Themes | The theme has to call the module's hook once (one line, see below) |

The module overrides no core class and creates three database tables, removed when it is uninstalled.

## Installation

1. Upload and install the module from **Modules → Module Manager**.
2. Add the hook to the category template of your theme, below the product list:
   `{hook h='displayM4pCategoryFaq' id_category=$category.id}`
3. Open **Catalog → Category FAQ** and add the first question.

A question that does not appear on the front office is either switched off or assigned to a different category than the one being viewed; the back-office list shows both.

## Configuration options

The module has no settings page. Every question carries its own:

| Field | Description |
|---|---|
| **Question** | Phrased the way customers ask it. |
| **Answer** | The answer in the first sentence, details after it. |
| **Categories** | The categories whose listing shows the question. |
| **Position** | Lower numbers come first; empty adds the question at the end. |
| **Enabled** | A switched-off question is stored and rendered nowhere. |

## Frequently asked questions

**Does this replace a CMS page with questions?**
No. A CMS page is a separate destination; these answers are on the page the customer is already reading. Both can coexist, but the same question should not carry FAQPage data twice.

**Will the structured data be shown in search results?**
That is Google's decision, and it currently shows FAQ rich results for a limited set of sites. The data is valid either way, and AI assistants read it too.

**What happens to the questions if I uninstall the module?**
They are deleted with the module's three tables. Export them first if you want to keep them.

---

**Keywords:** prestashop category faq, prestashop faq module, prestashop faqpage structured data, prestashop category content, prestashop seo category page

## License

MIT – see [LICENSE](LICENSE). Free to use commercially, fork and modify; keep the copyright notice.

## Contributing

Bug reports and pull requests are welcome – see [CONTRIBUTING.md](CONTRIBUTING.md). For security
issues, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.

---

Built by [Nice Code](https://nice-code.com/pl/oferta/moduly-prestashop) – we build and maintain PrestaShop stores.

© Nice Code sp. z o.o. (Modules4Presta) – released under the MIT license.
