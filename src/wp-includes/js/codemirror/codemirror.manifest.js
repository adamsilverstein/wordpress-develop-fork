/* global require */
if ( ! window.wp ) {
	window.wp = {};
}
import { CSSLint } from 'csslint';
window.CSSLint    = CSSLint;
window.wp.CSSLint = CSSLint;

import { jsonlint } from 'jsonlint';
window.jsonlint    = jsonlint;
window.wp.jsonlint = jsonlint;

import { HTMLHint } from 'htmlhint';
window.HTMLHint    = HTMLHint;
window.wp.HTMLHint = HTMLHint;

import { JSHINT } from 'jshint';
window.JSHINT    = JSHINT;
window.wp.JSHINT = JSHINT;

require( './htmlhint-kses' );

var CodeMirror = require( '../../../../node_modules/codemirror/lib/codemirror' );
require( '../../../../node_modules/codemirror/lib/codemirror.js' );
require( '../../../../node_modules/codemirror/keymap/emacs.js' );
require( '../../../../node_modules/codemirror/keymap/sublime.js' );
require( '../../../../node_modules/codemirror/keymap/vim.js' );
require( '../../../../node_modules/codemirror/addon/hint/show-hint.js' );
require( '../../../../node_modules/codemirror/addon/hint/anyword-hint.js' );
require( '../../../../node_modules/codemirror/addon/hint/css-hint.js' );
require( '../../../../node_modules/codemirror/addon/hint/html-hint.js' );
require( '../../../../node_modules/codemirror/addon/hint/javascript-hint.js' );
require( '../../../../node_modules/codemirror/addon/hint/sql-hint.js' );
require( '../../../../node_modules/codemirror/addon/hint/xml-hint.js' );
require( '../../../../node_modules/codemirror/addon/lint/lint.js' );
require( '../../../../node_modules/codemirror/addon/lint/css-lint.js' );
require( '../../../../node_modules/codemirror/addon/lint/html-lint.js' );
require( '../../../../node_modules/codemirror/addon/lint/javascript-lint.js' );
require( '../../../../node_modules/codemirror/addon/lint/json-lint.js' );
require( '../../../../node_modules/codemirror/addon/comment/comment.js' );
require( '../../../../node_modules/codemirror/addon/comment/continuecomment.js' );
require( '../../../../node_modules/codemirror/addon/fold/xml-fold.js' );
require( '../../../../node_modules/codemirror/addon/mode/overlay.js' );
require( '../../../../node_modules/codemirror/addon/edit/closebrackets.js' );
require( '../../../../node_modules/codemirror/addon/edit/closetag.js' );
require( '../../../../node_modules/codemirror/addon/edit/continuelist.js' );
require( '../../../../node_modules/codemirror/addon/edit/matchbrackets.js' );
require( '../../../../node_modules/codemirror/addon/edit/matchtags.js' );
require( '../../../../node_modules/codemirror/addon/edit/trailingspace.js' );
require( '../../../../node_modules/codemirror/addon/dialog/dialog.js' );
require( '../../../../node_modules/codemirror/addon/display/autorefresh.js' );
require( '../../../../node_modules/codemirror/addon/display/fullscreen.js' );
require( '../../../../node_modules/codemirror/addon/display/panel.js' );
require( '../../../../node_modules/codemirror/addon/display/placeholder.js' );
require( '../../../../node_modules/codemirror/addon/display/rulers.js' );
require( '../../../../node_modules/codemirror/addon/fold/brace-fold.js' );
require( '../../../../node_modules/codemirror/addon/fold/comment-fold.js' );
require( '../../../../node_modules/codemirror/addon/fold/foldcode.js' );
require( '../../../../node_modules/codemirror/addon/fold/foldgutter.js' );
require( '../../../../node_modules/codemirror/addon/fold/indent-fold.js' );
require( '../../../../node_modules/codemirror/addon/fold/markdown-fold.js' );
require( '../../../../node_modules/codemirror/addon/merge/merge.js' );
require( '../../../../node_modules/codemirror/addon/mode/loadmode.js' );
require( '../../../../node_modules/codemirror/addon/mode/multiplex.js' );
require( '../../../../node_modules/codemirror/addon/mode/simple.js' );
require( '../../../../node_modules/codemirror/addon/runmode/runmode.js' );
require( '../../../../node_modules/codemirror/addon/runmode/colorize.js' );
require( '../../../../node_modules/codemirror/addon/runmode/runmode-standalone.js' );
require( '../../../../node_modules/codemirror/addon/scroll/annotatescrollbar.js' );
require( '../../../../node_modules/codemirror/addon/scroll/scrollpastend.js' );
require( '../../../../node_modules/codemirror/addon/scroll/simplescrollbars.js' );
require( '../../../../node_modules/codemirror/addon/search/search.js' );
require( '../../../../node_modules/codemirror/addon/search/jump-to-line.js' );
require( '../../../../node_modules/codemirror/addon/search/match-highlighter.js' );
require( '../../../../node_modules/codemirror/addon/search/matchesonscrollbar.js' );
require( '../../../../node_modules/codemirror/addon/search/searchcursor.js' );
require( '../../../../node_modules/codemirror/addon/tern/tern.js' );
require( '../../../../node_modules/codemirror/addon/tern/worker.js' );
require( '../../../../node_modules/codemirror/addon/wrap/hardwrap.js' );
require( '../../../../node_modules/codemirror/addon/selection/active-line.js' );
require( '../../../../node_modules/codemirror/addon/selection/mark-selection.js' );
require( '../../../../node_modules/codemirror/addon/selection/selection-pointer.js' );
require( '../../../../node_modules/codemirror/mode/meta.js' );
require( '../../../../node_modules/codemirror/mode/clike/clike.js' );
require( '../../../../node_modules/codemirror/mode/css/css.js' );
require( '../../../../node_modules/codemirror/mode/diff/diff.js' );
require( '../../../../node_modules/codemirror/mode/htmlmixed/htmlmixed.js' );
require( '../../../../node_modules/codemirror/mode/http/http.js' );
require( '../../../../node_modules/codemirror/mode/javascript/javascript.js' );
require( '../../../../node_modules/codemirror/mode/jsx/jsx.js' );
require( '../../../../node_modules/codemirror/mode/markdown/markdown.js' );
require( '../../../../node_modules/codemirror/mode/gfm/gfm.js' );
require( '../../../../node_modules/codemirror/mode/nginx/nginx.js' );
require( '../../../../node_modules/codemirror/mode/php/php.js' );
require( '../../../../node_modules/codemirror/mode/sass/sass.js' );
require( '../../../../node_modules/codemirror/mode/shell/shell.js' );
require( '../../../../node_modules/codemirror/mode/sql/sql.js' );
require( '../../../../node_modules/codemirror/mode/xml/xml.js' );
require( '../../../../node_modules/codemirror/mode/yaml/yaml.js' );


window.wp.CodeMirror = CodeMirror;
