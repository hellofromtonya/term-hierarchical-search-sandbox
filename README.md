# Term Hierarchical Search Sandbox Plugin

This repository includes a Term Hierarchical Search utility file for Recursive SQL searching as well as a sandbox tester to test it out.

This plugin was built initially to help out a fellow developer.

## Dependencies

This plugin uses [Kint](https://github.com/kint-php/kint) to explore the values, i.e. `d()` and `ddd()`.  You can install the Kint package using either of these plugins:

1. [UpDevTools](https://github.com/KnowTheCode/UpDevTools)
2. [PHP Kint Debugger Plugin](https://wordpress.org/plugins/kint-php-debugger/) in the WordPress repository.

## Installation

1. In terminal, navigate to `{path to your sandbox project}/wp-content/plugins`.
2. Then type in terminal (or Git Bash): `git clone https://github.com/hellofromtonya/term-hierarchical-search-sandbox.git`.
3. Press enter or return.  The plugin starts cloning into your project.
4. When it's done, log into your WordPress website.
5. Go to Plugins and activate the "Term Hierarchical Search Testing Sandbox" plugin.
6. In the bootstrap file, [specify the meta key](https://github.com/hellofromtonya/term-hierarchical-search-sandbox/blob/master/bootstrap.php#L43) that you want to find.

The raw SQL that I used to test within Sequel Pro is [located here])(https://github.com/hellofromtonya/term-hierarchical-search-sandbox/blob/master/assets/raw.sql).

## Video Instruction

To walk you quickly through this tester, [here is a video](https://vimeo.com/226479594) that walks you through the recursive SQL technique.  

A hands-on lab will be produced soon that explains all of the code.