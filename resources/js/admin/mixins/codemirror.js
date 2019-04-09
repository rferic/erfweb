import { cmConfigDefault, cmModes } from './../structures/codemirror'
// require styles
import 'codemirror/lib/codemirror.css'
import 'codemirror/theme/monokai.css'
import 'codemirror/mode/javascript/javascript.js'
import 'codemirror/mode/css/css.js'
import 'codemirror/mode/htmlmixed/htmlmixed.js'
// require active-line.js
import 'codemirror/addon/selection/active-line.js'
// styleSelectedText
import 'codemirror/addon/selection/mark-selection.js'
import 'codemirror/addon/search/searchcursor.js'
// hint
import 'codemirror/addon/hint/show-hint.js'
import 'codemirror/addon/hint/show-hint.css'
import 'codemirror/addon/hint/javascript-hint.js'
import 'codemirror/addon/selection/active-line.js'
// highlightSelectionMatches
import 'codemirror/addon/scroll/annotatescrollbar.js'
import 'codemirror/addon/search/matchesonscrollbar.js'
import 'codemirror/addon/search/searchcursor.js'
import 'codemirror/addon/search/match-highlighter.js'
// keyMap
import 'codemirror/mode/clike/clike.js'
import 'codemirror/addon/edit/matchbrackets.js'
import 'codemirror/addon/edit/closebrackets.js'
import 'codemirror/addon/comment/comment.js'
import 'codemirror/addon/dialog/dialog.js'
import 'codemirror/addon/dialog/dialog.css'
import 'codemirror/addon/search/searchcursor.js'
import 'codemirror/addon/search/search.js'
import 'codemirror/keymap/sublime.js'
// foldGutter
import 'codemirror/addon/fold/foldgutter.css'
import 'codemirror/addon/fold/brace-fold.js'
import 'codemirror/addon/fold/comment-fold.js'
import 'codemirror/addon/fold/foldcode.js'
import 'codemirror/addon/fold/foldgutter.js'
import 'codemirror/addon/fold/indent-fold.js'
import 'codemirror/addon/fold/markdown-fold.js'
import 'codemirror/addon/fold/xml-fold.js'
// display
import 'codemirror/addon/display/autorefresh.js'

const codemirrorMixin = {
    data () {
        return {
            cmConfigDefault: {
                tabSize: 4,
                styleActiveLine: true,
                lineNumbers: true,
                styleSelectedText: true,
                line: true,
                foldGutter: true,
                gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                //highlightSelectionMatches: { showToken: /\w/, annotateScrollbar: true },
                mode: 'text/javascript',
                lineWrapping: true,
                theme: 'monokai',
                hintOptions:{
                    completeSingle: false
                },
                keyMap: 'sublime',
                matchBrackets: true,
                showCursorWhenSelecting: true,
                extraKeys: { 'Ctrl': 'autocomplete' },
                autoCloseBrackets: true,
                autoRefresh: true
            }
        }
    },
    methods: {
        // Getters
        getCodemirrorConfig ( language ) {
            let config = this.clone(this.cmConfigDefault)
            config.mode = cmModes[language]
            return config
        }
    }
}

export default codemirrorMixin
