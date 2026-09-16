---
name: rapidez-core-colors
description: How to use the Rapidez color classes with Tailwind CSS, use this when you're working with Tailwind CSS classes
---

# Rapidez Colors

Check the theming docs at https://docs.rapidez.io/llms.txt for all information and available colors. Try to use the Rapidez color classes as much as possible!

## Examples

This is how the Rapidez color classes should be used:

```
<body class="text">
    <div class="flex">
        <div class="w-2/3 bg border rounded p-3">
            <p>Lorem ipsum</p>
            
            <ul class="text-emphasis">
                <li>USP</li>
                <li>USP</li>
                <li class="text-primary">USP</li>
            </ul>

            {{-- button secondary --}}
            <button class="bg-secondary text-secondary-text">
                Call to action
            </button>

            <div class="bg-emphasis border rounded p-3">
                Lorem ipsum
            </div>

            {{-- border color needs an override with Tailwind Forms --}}
            <input type="text" class="border border-default focus:border-emphasis disabled:border-muted">
        </div>
        <div class="w-1/3 bg-muted border border-emphasis">
            <ul>
                <li><a href="#">Filter</a></li>
                <li><a href="#" class="text-muted">Filter disabled</a></li>
                <li><a href="#" class="text-emphasis">Filter active</a></li>
            </ul>
        </div>
    </div>
</body>
```

## Things NOT to do

- `text-foreground`, you should just use: `text`. The CSS variables names like `foreground` should not be used
- `bg-background-emphasis/10`, you should use: `bg-emphasis/10`
- `border border-border`, you should just use: `border` as we already set the default border color

