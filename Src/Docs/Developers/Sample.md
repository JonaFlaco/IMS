

<code class="block">
empty code
</code>


Aesthetic Table

| header 1 | header 2 |
| -------- | -------- |
| cell 1.1 | cell 1.2 |
| cell 2.1 | cell 2.2 |




Aligned Table

| header 1 | header 2 | header 2 |
| :------- | :------: | -------: |
| cell 1.1 | cell 1.2 | cell 1.3 |
| cell 2.1 | cell 2.2 | cell 2.3 |




Atx Heading

# h1

## h2

### h3

#### h4

##### h5

###### h6

####### not a heading

# closed h1 #

#




Automatic Link

<http://example.com>




Code Block

    <?php

    $message = 'Hello World!';
    echo $message;

---

    > not a quote
    - not a list item
    [not a reference]: http://foo.com




Code Span

a `code span`

`this is also a codespan` trailing text

`and look at this one!`

single backtick in a code span: `` ` ``

backtick-delimited string in a code span: `` `foo` ``

`sth `` sth`




Compound Blockquote

> header
> ------
>
> paragraph
>
> - li
>
> ---
>
> paragraph




Compound Emphasis

_`code`_ __`code`__

*`code`**`code`**`code`*




Compound List

- paragraph

  paragraph

- paragraph

  > quote




Deeply Nested List

- li
    - li
        - li
        - li
    - li
- li




Em Strong

___em strong___

___em strong_ strong__

__strong _em strong___

__strong _em strong_ strong__

***em strong***

***em strong* strong**

**strong *em strong***

**strong *em strong* strong**



Email

my email is <me@example.com>



Emphasis
HTML Output
HTML Output Source

_underscore_, *asterisk*, _one two_, *three four*, _a_, *b*

**strong** and *em* and **strong** and *em*

_line
line
line_

this_is_not_an_emphasis

an empty emphasis __ ** is not an emphasis

*mixed **double and* single asterisk** spans



Escaping

escaped \*emphasis\*.

`escaped \*emphasis\* in a code span`

    escaped \*emphasis\* in a code block

\\ \` \* \_ \{ \} \[ \] \( \) \> \# \+ \- \. \!

_one\_two_ __one\_two__

*one\*two* **one\*two**




Fenced Code Block

```
<?php

$message = 'fenced code block';
echo $message;
```

~~~
tilde
~~~

```php
echo 'language identifier';
```



Horizontal Rule

---

- - -

   - - -

***

___



Html Comment

<!-- single line -->

paragraph

<!-- 
  multiline -->

paragraph


Html Entity

&amp; &copy; &#123;


Image Reference

![Markdown Logo][image]

[image]: /md.png

![missing reference]


Image Title

![alt](/md.png "title")

![blank title](/md.png "")



Implicit Reference

an [implicit] reference link

[implicit]: http://example.com

an [implicit][] reference link with an empty link definition

an [implicit][] reference link followed by [another][]

[another]: http://cnn.com

an [explicit][example] reference link with a title

[example]: http://example.com "Example"




Inline Link

[link](http://example.com)

[link](/url-(parentheses)) with parentheses in URL 

([link](/index.php)) in parentheses

[`link`](http://example.com)

[![MD Logo](http://parsedown.org/md.png)](http://example.com)

[![MD Logo](http://parsedown.org/md.png) and text](http://example.com)



Inline Link Title

[single quotes](http://example.com 'Title')

[double quotes](http://example.com "Title")

[single quotes blank](http://example.com '')

[double quotes blank](http://example.com "")

[space](http://example.com "2 Words")

[parentheses](http://example.com/url-(parentheses) "Title")



Inline Title

[single quotes](http://example.com 'Example') and [double quotes](http://example.com "Example")


Lazy Blockquote

> quote
the rest of it

> another paragraph
the rest of it



Lazy List

- li
the rest of it




Multiline List Paragraph

- li

  line
  line



Nested Block-level Html

<div>
_parent_
<div>
_child_
</div>
<pre>
_adopted child_
</pre>
</div>

_outside_


Ordered List

1. one
2. two

repeating numbers:

1. one
1. two

large numbers:

123. one



Paragraph List

paragraph
- li
- li

paragraph

   * li
   
   * li


Reference Title

[double quotes] and [single quotes] and [parentheses]

[double quotes]: http://example.com "example title"
[single quotes]: http://example.com 'example title'
[parentheses]: http://example.com (example title)
[invalid title]: http://example.com example title




Self-closing Html

<hr>
paragraph
<hr/>
paragraph
<hr />
paragraph
<hr class="foo" id="bar" />
paragraph
<hr class="foo" id="bar"/>
paragraph
<hr class="foo" id="bar" >
paragraph



Separated Nested List

- li

    - li
    - li



Setext Header

h1
==

h2
--

single character
-

not a header

------------



Simple Blockquote

> quote

indented:
   > quote

no space after `>`:
>quote




Simple Table
HTML Output
HTML Output Source

header 1 | header 2
-------- | --------
cell 1.1 | cell 1.2
cell 2.1 | cell 2.2

---

header 1 | header 2
:------- | --------
cell 1.1 | cell 1.2
cell 2.1 | cell 2.2



Span-level Html

an <b>important</b> <a href=''>link</a>

broken<br/>
line

<b>inline tag</b> at the beginning

<span>http://example.com</span>



Sparse Dense List

- li

- li
- li


Sparse Html

<div>
line 1

line 2
line 3

line 4
</div>


Sparse List

- li

- li

---

- li

    - indented li



Special Characters

AT&T has an ampersand in their name

this & that

4 < 5 and 6 > 5

<http://example.com/autolink?a=1&b=2>

[inline link](/script?a=1&b=2)

[reference link][1]

[1]: http://example.com/?a=1&b=2


Strikethrough

~~strikethrough~~

here's ~~one~~ followed by ~~another one~~

~~ this ~~ is not one neither is ~this~



Strong Em

*em **strong em***

***strong em** em*

*em **strong em** em*

_em __strong em___

___strong em__ em_

_em __strong em__ em_



Tab-indented Code Block

	<?php
	
	$message = 'Hello World!';
	echo $message;

	echo "following a blank line";


Table Inline Markdown

| _header_ 1   | header 2     |
| ------------ | ------------ |
| _cell_ 1.1   | ~~cell~~ 1.2 |
| `|` 2.1      | \| 2.2       |
| `\|` 2.1     | [link](/)    |



[reference link][1]

[1]: http://example.com

[one][website] with a semantic name

[website]: http://example.com

[one][404] with no definition

[multiline
one][website] defined on 2 lines

[one][Label] with a mixed case label and an upper case definition

[LABEL]: http://example.com

[one]
[1] with the a label on the next line

[`link`][website]



Unordered List

- li
- li

mixed markers:

* li
+ li
- li



Untidy Table

| header 1 | header 2          |
| ------------- | ----------- |
| cell 1.1   | cell 1.2 |
|    cell 2.1 | cell 2.2     |


Url Autolinking

an autolink http://example.com

inside of brackets [http://example.com], inside of braces {http://example.com},  inside of parentheses (http://example.com)

trailing slash http://example.com/ and http://example.com/path/


Whitespace


    

    code
