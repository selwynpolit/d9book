# Developing Drupal at your Fingertips locally

The site is built with [VitePress](https://vitepress.dev) using Node.js and pnpm, and is deployed to GitHub Pages by GitHub Actions.

- Book pages (Markdown): `book/`
- VitePress config and theme: `.vitepress/`
- Build output (git-ignored): `dist/`
- Working branch: `gh-pages`

## 1. One-time setup on a Mac

### Homebrew and Git

```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
brew install git
```

### Node.js (via nvm)

`package.json` requires Node `>=26` and CI builds with Node 26. Use `nvm` so you can switch versions per project.

```bash
brew install nvm
mkdir -p ~/.nvm
```

Add the nvm lines Homebrew prints to your `~/.zshrc` (typically `export NVM_DIR="$HOME/.nvm"` plus the two `source` lines), then open a new terminal and run:

```bash
nvm install 26
nvm alias default 26
node -v   # should print v26.x.x
```

> **Node must be new enough.** rolldown (used by VitePress 2) ships its native code as optional packages that declare `node: ^20.19.0 || >=22.12.0`. On an older Node, pnpm silently skips them and the build fails with `Cannot find native binding`.

### pnpm

```bash
brew install pnpm
pnpm -v
```

The `packageManager` field in `package.json` pins the exact pnpm version for the project (currently `pnpm@12.8.1`). pnpm switches to that version automatically. To move the pin to a newer pnpm release:

```bash
pnpm self-update latest   # also rewrites "packageManager" in package.json
```

Keep `brew upgrade pnpm` reasonably current. Very old pnpm versions can't read `pnpm-workspace.yaml` (symptom: `packages field missing or empty`).

## 2. Get a local copy of the site

```bash
mkdir -p ~/Sites && cd ~/Sites
git clone git@github.com:selwynpolit/d9book.git
cd d9book
git checkout gh-pages
nvm use 26
pnpm install
```

To contribute from a fork, fork on GitHub, clone your fork, and open pull requests against `gh-pages`.

## 3. Daily commands

| Command                | What it does                                                |
| ---------------------- | ----------------------------------------------------------- |
| `pnpm run book:dev`    | Dev server with live reload (usually http://localhost:5173) |
| `pnpm run book:build`  | Production build into `dist/` (same as CI/CD runs)          |
| `pnpm run book:preview`| Serve the built `dist/` locally to check the final output   |

Run `pnpm run book:build` before pushing. It catches broken links and Markdown or Vue template errors that the dev server can be lenient about.

## 4. Updating dependencies

Dependabot opens a grouped PR quarterly (see `.github/dependabot.yml`). To update by hand:

```bash
nvm use 26
pnpm update
pnpm run book:build
git add package.json pnpm-lock.yaml
git commit -m "Update pnpm dependencies"
```

CI and CD install with `--frozen-lockfile`, so `pnpm-lock.yaml` must always be committed together with `package.json`.

## 5. Publishing changes (and how auto-deploy works)

```bash
git status
git add book/some-page.md
git commit -m "Update some-page.md"
git push origin gh-pages
```

Pushing to `gh-pages` triggers two workflows automatically. Pushes that only change `README.md` or `LICENSE.md` are ignored.

1. **CI** (`.github/workflows/ci.yml`) checks out the repo, sets up Node 26 and pnpm, runs `pnpm i --frozen-lockfile`, then `pnpm book:build`. This is a build check only, and nothing is published. It also runs on pull requests to `gh-pages`.
2. **CD** (`.github/workflows/cd.yml`) has three jobs:
   - **cleanup**: removes old deployments and workflow runs older than 7 days.
   - **build**: installs and builds as above, with `VITE_GTAG` (the Google Analytics ID, set as a repository variable) available, then uploads `./dist` as a Pages artifact.
   - **deploy**: publishes the artifact to GitHub Pages using `actions/deploy-pages`.

The site is live at https://www.drupalatyourfingertips.com within a minute or two of a successful CD run. Watch progress in the repo's **Actions** tab, or via the CI and CD badges in `README.md`. Either workflow can also be started manually from the Actions tab (**Run workflow**).

The `pnpm/action-setup` step has no `version:` input, so CI and CD use whatever pnpm version `packageManager` pins in `package.json`. If that pin is a broken release, both workflows fail until it's changed.

GitHub Pages must be configured with **Settings > Pages > Source: GitHub Actions** for the deploy job to work.

## 6. Troubleshooting

| Symptom                                                  | Cause and fix                                                                                                                       |
| -------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------- |
| `ERR_PNPM_BROKEN_PNPM_RELEASE`                           | `packageManager` pins a pnpm release that was pulled. Run `pnpm self-update latest`.                                                |
| `packages field missing or empty`                        | pnpm is too old for `pnpm-workspace.yaml`. Run `brew upgrade pnpm`.                                                                 |
| `Cannot find native binding` / `@rolldown/binding-...`   | Node is too old, so the binding was skipped. Run `nvm use 26`, then `rm -rf node_modules && pnpm install`.                          |
| Wrong Node after opening a new terminal                  | Run `nvm alias default 26` so new shells use it.                                                                                    |
| Build works locally, fails in CI                         | Check that `pnpm-lock.yaml` is committed and in sync with `package.json`.                                                           |
