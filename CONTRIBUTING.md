*[A canonical link to the file you're currently reading.](https://github.com/Maikuolan/Common/blob/v2/CONTRIBUTING.md)*

## **Want to help?**

If you want to report bugs, suggest new features, new classes, etc, you should start at the [issues](https://github.com/Maikuolan/Common/issues) page. Any already reported bugs, requested features, classes, progress checklists, discussions, etc should be available there, and everyone is welcome to participate and contribute. If you have something to add, whether for an existing issue or a new issue, feel free to do so.

The project is open-source, released by its author under the [GNU/GPLv2 license](https://github.com/Maikuolan/Common/blob/v2/LICENSE.txt), so if you want to fork/copy the project and make your own modifications, you're welcome to do so. If you feel that your modifications could be beneficial to the project and its community, consider sending a [pull request](https://github.com/Maikuolan/Common/pulls) (include a description with pull requests, so that we know what we're looking at) so that we can review your modifications and consider integrating them as part of the project.

Anyone interested in contributing to the project is encouraged to do so.

### **Not sure how to help? Here are some things you could do.**
- **Have any unused free time to spare?** Projects always consume time, especially free, open-source projects. If you're able to help out at all with some of your time, progress could move forward a little faster.
- **Good at coding and refactoring?** Check the issues page and see whether there's anything we're stuck on, or whether there's anything you could help us out with. Consider reviewing the codebase. If you see something that you think could be improved, feel free to have a go at it.
- **Good at thinking of new ideas or new features, or have something specific in mind that you'd like to see implemented?** Sharing ideas is always welcomed, and if we like your ideas, we might implement them.
- **Good at unit testing?** Consider helping us implement better tests.
- **Good at benchmarking?** If you'd be willing to test the codebase on your system, or willing to benchmark it, let us know what works, what doesn't work, and consider sharing your benchmarks and other findings, to help us find ways to improve the codebase and the project overall.
- **Good at pentesting? Good at finding bugs?** If you find any problems, any bugs, anything dangerous in the codebase, etc, report it privately, as described in [`SECURITY.md`](https://github.com/Maikuolan/Common/blob/v2/SECURITY.md).
- **Good at writing documentation?** If you'd be willing to review our documentation, to add anything that could be missing, to improve on it at all, this would be welcome.
- **Good at writing guides and tutorials?** Some users prefer guides and tutorials over documentation, and want practical, step by step tutorials, on how to achieve specific goals, or guides to provide perspective from other users, about the best ways to utilise the project and its classes.
- **Good at reviewing? Good at quality control?** If there are any pending pull requests that haven't yet been reviewed or accepted, please consider offering to review them, offer feedback, suggestions, etc.
- **Good at social networking? Good at sharing?** Please consider spreading word about the project. If you know anyone that you think could benefit from the project, let them know about it. Write about the project, review it, and share it onward.

### **The common classes package code-style guidelines.**

From here onward in this document, the words "MUST", "MUST NOT", "SHOULD", "SHOULD NOT", and "MAY" are to be interpreted in accordance with *[RFC 2119](https://www.ietf.org/rfc/rfc2119.txt)*.

The common classes package adopts and adheres to *[PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/)* (which extends *[PSR-1: Basic Coding Standard](https://www.php-fig.org/psr/psr-1/)* and *[PSR-2: Coding Style Guide](https://www.php-fig.org/psr/psr-2/) [deprecated; superseded by PSR-12]*), and *[PSR-4: Autoloader](https://www.php-fig.org/psr/psr-4/)* (as closely as possible, and to the best of our abilities). Whenever possible, any and all future contributions to the codebase SHOULD adopt and adhere to these standards too. If you're unfamiliar with these, I would recommend reading them carefully.

The common classes package adopts *[PHPDoc](https://docs.phpdoc.org/references/phpdoc/index.html)* to annotate and describe PHP code. If you're unfamiliar with this, I would recommend reading the linked documentation carefully.

To avoid unnecessary complexity, red tape, or hindrances to future contributions, future contributions will be measured against any other PSRs or style guides not yet mentioned, IF and WHEN it becomes relevant and useful to do so (i.e., we'll cross those bridges when we get to them). To be more concise and to avoid unnecessary duplication in this document, any points already covered, either by this document, or by any linked documents (e.g., the PSRs adopted by this project), won't be repeated.

Excluding some test fixtures where it's necessary, all project files (including all code, markup, etc) MUST utilise UTF-8 encoding (without BOM), and MUST NOT contain any trailing whitespace.

When writing code or markup, contributors SHOULD aim for maximum readability, and maximum maintainability for their contributions.

All PHP functions, methods, and closures MUST be properly and accurately annotated by an attached DocBlock. All PHP files MUST contain a DocBlock header, to describe the general nature of the file. All attached DocBlocks SHOULD be concise, and SHOULD NOT be overly verbose (i.e., containing more than necessary). All comments in all PHP files and code MUST conform to PHPDoc (i.e., as DocComments). Where the purpose and intent of some particular code is highly obtuse, unclear, or not easily understood, it SHOULD be commented. Where the purpose and intent of some particular code is highly transparent and easily understood (and where not otherwise already dictated by this document), it SHOULD NOT be commented. All annotation, and all comments, MUST be written in English.

Pull requests are checked by the test suite (`composer test`) and by PHP-CS-Fixer, so if you can, please run both before submitting to catch potential problems. If you can't, the CI-enforced checks should catch most problems, but approval and merging may be delayed while we work through them.

### **The common classes package version release guidelines.**

The common classes package adopts and adheres to *[SemVer](https://semver.org/) (Semantic Versioning)*. If you're unfamiliar with this, I would recommend reading the linked documentation carefully.

Extending upon the rules provided by SemVer, the following are to be interpreted as backwards-incompatible changes:
- Anything that increases the minimum PHP version requirement (up to and including v1: PHP 5.4; v2: PHP 7.2; when this line becomes stale or wherever this document may be lacking, the `composer.json` file attached to each major version's branch specifies its minimum PHP version requirement).
- The permanent removal of any specific class.
- Any backwards-incompatible changes to the overall way that a class is intended to be used.

When tagging a new release, the "tag version" MUST be formatted as "vM.m.p" (where, per SemVer definitions, "M" is the MAJOR version number, "m" is the MINOR version number, and "p" is the PATCH version number), and the "release title" MUST be formatted as "Common Classes Package vM.m.p". The release description SHOULD contain all relevant changes implemented since the previous most recent release targeted to the same branch. The release description SHOULD NOT contain anything irrelevant to the release.

### **The common classes package documentation guidelines.**

All formal documentation for the common classes package can be found in the `docs` directory of the repository. Each major version branch contains its own documentation (to keep everything nicely bundled together in the same package, and in case any significant differences emerge between different major versions).

### **When sending pull requests, which branch should I target?**

There's no "master" or "main", and instead, branches are named according to major version. Therefore, target the branch which corresponds to the major version you intend to target ("vX", where "X" is the major version number, e.g., v1, v2, v3). If a branch exists for a specific feature, a specific planned task, or similar, and that's the thing that you're working on, target that branch instead. Otherwise, if no branch exists for the particular major version you're wanting to affect, or if you feel that you need to be able to target your pull request somewhere else, you can ask for a new branch to be created.

### Policy on AI-assisted contributions.

All contributions (code, issues, reports, and so on) are reviewed to the same standard and with the same tone, regardless of origin and regardless of whether AI assistance was used. A good contribution is a good contribution. If it follows the contributor guidelines, is legally sound (including licensing), benefits the project, and is something I can properly review (including being able to ask you questions about it), it will receive a proper review and be acted on accordingly.

Some projects have banned AI-assisted contributions entirely, because floods of low-effort, guideline-ignoring submissions (including bogus security advisories and nonsensical issues) exceed what their maintainers can review. In that situation, I'd do the same. This project hasn't faced that, so there's no ban at this time. I reserve the right to change this policy if circumstances change.

#### Accountability.
- You are the contributor, whether or not you used AI. You're responsible for your submission being correct, tested where that's possible, and properly licensed.
- You must have reviewed and understood what you submit, and be able to explain it. Answer review questions yourself, in your own words.
- Fully automated submissions (a tool opening issues, reports or pull requests without a human reviewing them first) won't be accepted.
- Low-effort submissions may be closed without detailed review, and repeated ones may result in a block.
- Security reports should follow [`SECURITY.md`](https://github.com/Maikuolan/Common/blob/v2/SECURITY.md) and must be reproducible. Don't paste non-public security details, or real malware or user data, into AI tools or public issues.

#### Disclosure.
**Optional:** If you used AI assistance for a commit, please say so with a trailer such as `Co-Authored-By: ToolName <address>` or `Assisted-By: ToolName <address>`, whichever best fits the nature of the assistance (*see also: "[Understanding Git Commit Trailers](https://www.simonegigante.com/posts/commit-trailers/)"*). This isn't enforceable or reliably verifiable, so it's a courtesy and not a requirement, but it's appreciated for traceability.

*Example:*
```
git commit -m "Commit title, e.g., added feature X.

Changelog excerpt:
- Commit description, e.g., added feature X, introducing functionality Z to
  the codebase.

Co-Authored-By: ToolName <address>"
```

#### Licensing and intellectual property.
You're responsible for having the right to submit your contribution under the project's licence. That includes making sure the terms of any AI tool you used allow it, and that its output doesn't reproduce copyrighted or incompatibly licensed code from elsewhere.

### Licensing for contributions in a broader context.

Unless you state otherwise at the time a contribution is made, you're assumed to agree with the contribution falling under the same licensing schema as the project as a whole and asserting that you have the legal right to do so. You agree to not contribute any commercially-licensed content or content whose licensing is incompatible with this project. If your contribution requires a licensing schema which differs from the project as a whole, assuming you have the right to do so, you should mention this at the time the contribution is made so that it can be discussed.

---


Last Updated: 27 November 2026 (2026.11.27).
