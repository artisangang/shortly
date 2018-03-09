## shortly
```php
<?php

require './vendor/autoload.php';


function test($param1, $param2) {

	return "The first argument is $param1 and $param2 is the second one.";

}

$s = new artisangang\shortly\ServiceProvider();

$s->allowGlobalFunctions();


//$s->parseAttributesOnly();

ob_start();

?>

<p>[test:animal,Sea Horse]</p>
<p><a href="[test:animal,Sea Horse]">HyperLink of this tag is: [test:animal,Sea Horse]</a></p>
<p><a href="[url:/images/logo.png]">Not Found Function output will be commented.</a></p>
<p>[slideshow]</p>
<p>ShortTag not found: [cart]</p>
<p>[slider size='large' title="Slider parse by shortcode"]</p>
<p>Invalid short code: [size=large title=Slider parse by shortcode animation=slide]</p>
<p>[showcase size='medium' title="Showcase parsed by shortly" animation='fade']</p>
<p>Context Tag: [welcome]Admin[/welcome]</p>
<p>[welcome to="Portal" date="true"]I m Guest[/welcome]</p>
<p>Context Tag: [welcome]Global Function: [test:animal,Sea Horse] and [slider size='large' title="Slider parse by shortcode" animation=slide] in context tag.[/welcome]</p>
<?php

$content = ob_get_clean();
/*
echo $s->parse("<strong>[test:animal,Sea Horse]</strong>");
echo $s->parse('<a href="[test:animal,Sea Horse]">See Source of [test:animal,Sea Horse]...</a>');
echo $s->parse('<a href="[url:/images/logo.png]">See Source of [test:animal,Sea Horse]...</a>');
*/
class SliderTag extends artisangang\shortly\Tag {

	
	public function name() {
		return 'slider';
	}

	public function attributes() {
		return ['id' => uniqid(), 'animation' => 'fade', 'size' => 'md', 'autoplay' => '', 'title' => ''];
	}

	public function parse($content = null) {
		return 'Slider tag working! with following attributes -> ' . json_encode($this->attributes);
	}

}

class ShowCaseTag extends artisangang\shortly\Tag {

	
	public function name() {
		return 'showcase';
	}

	public function attributes() {
		return ['id' => uniqid(), 'animation' => 'fade', 'size' => 'md', 'autoplay' => '', 'title' => ''];
	}

	public function parse($content = null) {
		return 'showcase tag working! with following attributes -> ' . json_encode($this->attributes);
	}

}

class WelcomeTag extends artisangang\shortly\Tag {


	
	public function name() {
		return 'welcome';
	}

	public function attributes() {
		return ['to', 'date'];
	}

	public function parse($content = null) {
		return "Hello $content! with following attributes -> " . json_encode($this->attributes);
	}

}

class SlideShowTag extends artisangang\shortly\Tag {


	
	public function name() {
		return 'slideshow';
	}

	public function attributes() {
		return [];
	}

	public function parse($content = null) {
		return "<marquee>This is parsed by slide show shortcode.</marquee>";
	}

}

$s->addTag(new SliderTag);
$s->addTag(new ShowCaseTag);
$s->addTag(new WelcomeTag);
$s->addTag(new SlideShowTag);

echo $s->parse($content);
```