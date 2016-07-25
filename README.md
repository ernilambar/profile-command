runcommand/profile
==================

Profile the performance of a WordPress request.

[![Build Status](https://travis-ci.org/runcommand/profile.svg?branch=master)](https://travis-ci.org/runcommand/profile)

Quick links: [Using](#using) | [Installing](#installing) | [Contributing](#contributing)

## Using

~~~
wp profile [--url=<url>] [--fields=<fields>] [--format=<format>]
~~~

Monitors aspects of the WordPress execution process to display key
performance indicators for audit.

```
$ wp profile
+------------+----------------+-------------+------------+------------+-----------+--------------+
| scope      | execution_time | query_count | query_time | hook_count | hook_time | memory_usage |
+------------+----------------+-------------+------------+------------+-----------+--------------+
| bootstrap  | 2.34255s       | 15          | 0.00386s   | 2835       | 0.11172s  | 45mb         |
| main_query | 0.01155s       | 3           | 0.0004s    | 78         | 0.00117s  | 45.75mb      |
| template   | 0.32768s       | 174         | 0.0174s    | 7824       | 0.08106s  | 49.25mb      |
| total      | 2.68269s       | 192         | 0.02165s   | 10737      | 0.19395s  | 49.25mb      |
+------------+----------------+-------------+------------+------------+-----------+--------------+
```

**OPTIONS**

	[--url=<url>]
		Execute a request against a specified URL. Defaults to the home URL.

	[--fields=<fields>]
		Display one or more fields.

	[--format=<format>]
		Render output in a particular format.
		---
		default: table
		options:
		  - table
		  - json
		  - yaml
		  - csv
		---

## Installing

Installing this package requires WP-CLI v0.23.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with `wp package install runcommand/profile`.

## Contributing

We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.

### Reporting a bug

Think you’ve found a bug? We’d love for you to help us get it fixed.

Before you create a new issue, you should [search existing issues](https://github.com/runcommand/profile/issues?q=label%3Abug%20) to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version.

Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue](https://github.com/runcommand/profile/issues/new) with the following:

1. What you were doing (e.g. "When I run `wp post list`").
2. What you saw (e.g. "I see a fatal about a class being undefined.").
3. What you expected to see (e.g. "I expected to see the list of posts.")

Include as much detail as you can, and clear steps to reproduce if possible.

### Creating a pull request

Want to contribute a new feature? Please first [open a new issue](https://github.com/runcommand/profile/issues/new) to discuss whether the feature is a good fit for the project.

Once you've decided to commit the time to seeing your pull request through, please follow our guidelines for creating a pull request to make sure it's a pleasant experience:

1. Create a feature branch for each contribution.
2. Submit your pull request early for feedback.
3. Include functional tests with your changes. [Read the WP-CLI documentation](https://wp-cli.org/docs/pull-requests/#functional-tests) for an introduction.
4. Follow the [WordPress Coding Standards](http://make.wordpress.org/core/handbook/coding-standards/).

*This README.md is generated dynamically from the project's codebase using `wp scaffold package-readme` ([doc](https://github.com/wp-cli/scaffold-package-command#wp-scaffold-package-readme)). To suggest changes, please submit a pull request against the corresponding part of the codebase.*
