# Deploying the Blog

Follow these steps to configure FTP access and automate deployment with GitHub Actions.

## 1. Create a restricted FTP user
1. Log into your o2switch cPanel.
2. Go to **FTP Accounts** and create a new user.
3. Set the directory to your blog location, e.g. `/public_html/blog/`.
4. Save the credentials for use in GitHub Secrets.

## 2. Add repository secrets
1. In GitHub, open **Settings** → **Secrets and variables** → **Actions**.
2. Add the following secrets:
   - `FTP_SERVER` – your FTP hostname.
   - `FTP_USER` – the FTP username you created.
   - `FTP_PASS` – the corresponding password.

## 3. About runners
GitHub runs workflows on virtual machines called **runners**. Using `runs-on: ubuntu-latest` gives a fresh Linux machine hosted by GitHub for every run—plenty for this static site.

### Switching to self-hosted
If you later need your own runner:
1. Download the [GitHub runner](https://github.com/actions/runner/releases) on your server.
2. Run `./config.sh --url https://github.com/&lt;owner&gt;/&lt;repo&gt; --token &lt;token&gt;` and follow the prompts.
3. Start it with `./run.sh` and update the workflow to use `runs-on: [self-hosted, linux]`.

## 4. Workflow trigger
The workflow defined in `.github/workflows/publish.yml` runs on every push to the `main` branch. It sorts posts, builds `public/feed.xml`, and deploys `public/` via FTP automatically.

