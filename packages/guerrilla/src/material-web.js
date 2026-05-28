/**
 * Material Web Components (MD3) entry point.
 *
 * Add one import per component as new MD3 elements are used in blocks.
 * Vite tree-shakes this file — only imported components end up in the bundle.
 *
 * Full component list: https://material-web.dev/components/
 */

// --- Buttons ---
import '@material/web/button/filled-button.js';
import '@material/web/button/outlined-button.js';
import '@material/web/button/text-button.js';

// --- Cards (layout only — no dedicated <md-card> in MD3; use divs + elevation) ---

// --- Divider ---
import '@material/web/divider/divider.js';

// --- Icon ---
import '@material/web/icon/icon.js';

// --- Ripple (touch feedback) ---
import '@material/web/ripple/ripple.js';
