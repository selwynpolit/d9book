---
title: Contribute to this book
---

# Contributing to Drupal at your fingertips

This book is a quick reference for developers creating Drupal sites. Please remember that when you contribute, your audience is Drupal developers who are trying to find a quick answer to their questions. Try to make your contribution as succinct and clear as possible. I also prefer that the examples you use have code that actually works rather than pseudo code wherever possible.

Github has a very convenient set of tools which allow you to easily make changes via the web interface. You can also use the web interface to create a pull request. The easiest way to suggest changes is to use the "Edit this page on GitHub" link at the bottom of each page. This will take you to the source file for the page you are on. You can then click the pencil icon to edit the file.

![Edit this page on GitHub](/images/edit-this-page.png)

You can then make your changes and create a pull request. Please make sure that the target branch is `gh-pages`. This is the branch that is used to build the site.

Alternatively, you could start with forking the repo using the button at the top right of the page. Then you can make changes to your fork and create a pull request.

![Forking the repo](/images/fork-me.png)

When you create a pull request, please make sure that the target branch is `gh-pages`. This is the branch that is used to build the site. I will review the changes and merge them into the site if they are appropriate.

# Setting up and running a local copy of the site on your mac

This will let you make edits and see them in real time. It is useful for testing changes before submitting a pull request especially if you want to try some of the cool magic that [Vitepress](https://vitepress.dev/) can do. Check out the [Vitepress Markdown Extensions](https://vitepress.dev/guide/markdown#markdown-extensions) for more.

1. Clone the repo to your local e.g. d9book2
2. Install all the requirements with:

```sh
nvm install node
npm install -g pnpm
pnpm install
```

3. Start local dev server with:

```sh
pnpm run book:dev
```

This shows a local URL: `http://localhost:5173/` open it!

To build your site for production (which generates static files in the dist directory), you can use the following: (This doesn't seem necessary as the local dev server seems to do the same thing)

```sh
pnpm run book:build
```

## Resources

- [First contributions](https://github.com/firstcontributions/first-contributions) is a great place to start if you are new to contributing to open source projects.
