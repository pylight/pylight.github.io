Hypertext mechanics in BASH
===========================

For command line nerds. With love.

A sample is worth a thousand words:

	$ mkdir contents htdocs layouts
	$ echo '<h1>Hello World</h1>' > contents/Hello-World
	$ echo '<html><body>`content`</body></html>' > layouts/contents.html
	$ simsalabash
	$ firefox htdocs/index.html

Find more reasonable examples in examples/.

How does it work?
-----------------

For every file in contents/, the corresponding layout will get expanded into
htdocs/ where the output is stored under the name of the content file with
the extension of the layout file.

Layouts correspond to content files, when their name (without extension) does
match a component of the (relative) path of the content file. Matching starts
from the rear end with the file name.

In your content and/or layout files you may use shell expansions like

	$(command)

or

	`command`

or

	$((expression))

or

	Hi, this is $USER talking.

to combine your website.

simsalabash does provide the following handy commands:

	title    - prints the title which is the formatted file name
	           of the content file in process

	           usage: title


	nav      - prints a recursive standard list navigation

	           usage: [(UNFOLD|FLAT)=1] nav [PATH]

	           PATH - content path (optional, defaults to content/)
	           UNFOLD - unfold navigation, this will make a sitemap
	           FLAT - do not dive into subdirectories


	content  - prints the content of file in process

	           usage: content


	include  - include (and expand) another content/layout file

	           usage: include FILE...

	           FILE - path and name of file to include


Apart from that, you're free to invoke any command you like. For
example, you may do something like this:

	$FILE was written by `whoami`

$FILE holds the path and name of the content file in process.

How to extend simsalabash?
--------------------------

There are two places for additional source files:

	$HOME/.simsalabash/
	$PWD/lib/

Use the first location for general extensions you want to have always.
Use the latter for project specific extensions and modifications.

You can override existing functions to extend simsalabash or add
new ones to add further functionality.

How to use Markdown?
--------------------

Download Markdown.pl from

http://daringfireball.net/projects/markdown/

and put it into your path. Then paste this

	content()
	{
		include $FILE | Markdown.pl
	}

into a file in $HOME/.simsalabash/ to override and extend the default
function.

Purpose
-------

simsalabash aims to be a simple tool to combine a website from a bunch
of text files and generate a recursive standard list navigation for it.

It was designed to minify the effort to build and maintain small static
websites and targets the experienced BASH user.

simsalabash is not a Jack of all trades, though. Please check out its
advantages and disadvantages:

Advantages and principles
-------------------------

* Little to no configuration.
* Meaningful structure of content/ which mirrors your web site.
* Automatically generates a recursive standard list navigation.
* Easy sorting of navigation items with .nav files.
* The output directory is not temporary so assets and downloads won't
  get copied everytime you're doing an rsync.
* No dependencies beyond BASH, cat, mktemp, rm and probably tr (BASH < 4).

Disadvantages and limitations
-----------------------------

* Markdown and things like that are external.
* There's no integrated web server and never will be.
* BASH is slow (but fast enough for any web site of reasonable size).
* BASH is not object-oriented, nor easy, nor hip.

Alternatives
------------

Find a comprehensive list at [staticsitegenerators.net](http://staticsitegenerators.net/).
