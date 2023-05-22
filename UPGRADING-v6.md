## Update psalm to v5

Since fp4php/functional v6 Psalm integration ships as separate package.
Including the Psalm integration in the main fp4php/functional repository was a mistake.

For migration:

1. Remove old toolkit:

```shell
composer remove fp4php/psalm-toolkit
```

2. Install new psalm plugin:

```shell
composer require --dev fp4php/functional-psalm-plugin
```

3. Update psalm.xml:

```diff
<?xml version="1.0"?>
<psalm ...>
    ...
    <plugins>
-        <pluginClass class="Fp\Psalm\FunctionalPlugin"/>
-        <pluginClass class="Fp\PsalmToolkit\Toolkit\Plugin"/>
+        <pluginClass class="Fp\PsalmPlugin\FunctionalPlugin"/>
    </plugins>
</psalm>
```
