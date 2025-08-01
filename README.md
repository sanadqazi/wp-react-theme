# WP React Theme

A custom WordPress theme with integrated ReactJS components.

## 🚀 Installation

1. **Clone or Download** this theme into your Bedrock-based WordPress project:


2. **Install Babel (if not already installed globally)**

`npm install --save-dev @babel/core @babel/cli @babel/preset-react`


3. **Compile the React JSX**

From the theme root, run:

`npx babel react-src/my-react-app.jsx --out-file assets/js/my-react-app.js --presets=@babel/preset-react`

4. **Activate the Theme**

Log in to your WordPress admin panel and activate **WP React Theme**.

5. **Use the Shortcode**

To render the React app in a page/post:

`[render_react]`
