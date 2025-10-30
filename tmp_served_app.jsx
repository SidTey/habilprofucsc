import { createHotContext as __vite__createHotContext } from "/@vite/client";import.meta.hot = __vite__createHotContext("/resources/js/app.jsx");import __vite__cjsImport0_react_jsxDevRuntime from "/node_modules/.vite/deps/react_jsx-dev-runtime.js?v=572b04be"; const jsxDEV = __vite__cjsImport0_react_jsxDevRuntime["jsxDEV"];
import * as RefreshRuntime from "/@react-refresh";
const inWebWorker = typeof WorkerGlobalScope !== "undefined" && self instanceof WorkerGlobalScope;
let prevRefreshReg;
let prevRefreshSig;
if (import.meta.hot && !inWebWorker) {
  if (!window.$RefreshReg$) {
    throw new Error(
      "@vitejs/plugin-react can't detect preamble. Something is wrong."
    );
  }
  prevRefreshReg = window.$RefreshReg$;
  prevRefreshSig = window.$RefreshSig$;
  window.$RefreshReg$ = RefreshRuntime.getRefreshReg("C:/Users/penaj/Documents/larabel+react/resources/js/app.jsx");
  window.$RefreshSig$ = RefreshRuntime.createSignatureFunctionForTransform;
}
import __vite__cjsImport3_react from "/node_modules/.vite/deps/react.js?v=572b04be"; const React = __vite__cjsImport3_react.__esModule ? __vite__cjsImport3_react.default : __vite__cjsImport3_react;
import __vite__cjsImport4_reactDom_client from "/node_modules/.vite/deps/react-dom_client.js?v=572b04be"; const createRoot = __vite__cjsImport4_reactDom_client["createRoot"];
import "/resources/js/bootstrap.js";
import TestPage from "/resources/js/pages/TestPage.jsx";
function App() {
  return /* @__PURE__ */ jsxDEV("div", { className: "min-h-screen flex items-center justify-center", children: /* @__PURE__ */ jsxDEV(TestPage, {}, void 0, false, {
    fileName: "C:/Users/penaj/Documents/larabel+react/resources/js/app.jsx",
    lineNumber: 28,
    columnNumber: 13
  }, this) }, void 0, false, {
    fileName: "C:/Users/penaj/Documents/larabel+react/resources/js/app.jsx",
    lineNumber: 27,
    columnNumber: 5
  }, this);
}
_c = App;
const el = document.getElementById("app");
if (el) {
  createRoot(el).render(/* @__PURE__ */ jsxDEV(App, {}, void 0, false, {
    fileName: "C:/Users/penaj/Documents/larabel+react/resources/js/app.jsx",
    lineNumber: 35,
    columnNumber: 25
  }, this));
}
var _c;
$RefreshReg$(_c, "App");
if (import.meta.hot && !inWebWorker) {
  window.$RefreshReg$ = prevRefreshReg;
  window.$RefreshSig$ = prevRefreshSig;
}
if (import.meta.hot && !inWebWorker) {
  RefreshRuntime.__hmr_import(import.meta.url).then((currentExports) => {
    RefreshRuntime.registerExportsForReactRefresh("C:/Users/penaj/Documents/larabel+react/resources/js/app.jsx", currentExports);
    import.meta.hot.accept((nextExports) => {
      if (!nextExports) return;
      const invalidateMessage = RefreshRuntime.validateRefreshBoundaryAndEnqueueUpdate("C:/Users/penaj/Documents/larabel+react/resources/js/app.jsx", currentExports, nextExports);
      if (invalidateMessage) import.meta.hot.invalidate(invalidateMessage);
    });
  });
}

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJtYXBwaW5ncyI6IkFBUVk7Ozs7Ozs7Ozs7Ozs7Ozs7QUFSWixPQUFPQSxXQUFXO0FBQ2xCLFNBQVNDLGtCQUFrQjtBQUMzQixPQUFPO0FBQ1AsT0FBT0MsY0FBYztBQUVyQixTQUFTQyxNQUFNO0FBQ1gsU0FDSSx1QkFBQyxTQUFJLFdBQVUsaURBQ1gsaUNBQUMsY0FBRDtBQUFBO0FBQUE7QUFBQTtBQUFBLFNBQVMsS0FEYjtBQUFBO0FBQUE7QUFBQTtBQUFBLFNBRUE7QUFFUjtBQUFDQyxLQU5RRDtBQVFULE1BQU1FLEtBQUtDLFNBQVNDLGVBQWUsS0FBSztBQUN4QyxJQUFJRixJQUFJO0FBQ0pKLGFBQVdJLEVBQUUsRUFBRUcsT0FBTyx1QkFBQyxTQUFEO0FBQUE7QUFBQTtBQUFBO0FBQUEsU0FBSSxDQUFHO0FBQ2pDO0FBQUMsSUFBQUo7QUFBQUssYUFBQUwsSUFBQSIsIm5hbWVzIjpbIlJlYWN0IiwiY3JlYXRlUm9vdCIsIlRlc3RQYWdlIiwiQXBwIiwiX2MiLCJlbCIsImRvY3VtZW50IiwiZ2V0RWxlbWVudEJ5SWQiLCJyZW5kZXIiLCIkUmVmcmVzaFJlZyQiXSwiaWdub3JlTGlzdCI6W10sInNvdXJjZXMiOlsiYXBwLmpzeCJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgUmVhY3QgZnJvbSAncmVhY3QnO1xyXG5pbXBvcnQgeyBjcmVhdGVSb290IH0gZnJvbSAncmVhY3QtZG9tL2NsaWVudCc7XHJcbmltcG9ydCAnLi9ib290c3RyYXAnO1xyXG5pbXBvcnQgVGVzdFBhZ2UgZnJvbSAnLi9wYWdlcy9UZXN0UGFnZSc7XHJcblxyXG5mdW5jdGlvbiBBcHAoKSB7XHJcbiAgICByZXR1cm4gKFxyXG4gICAgICAgIDxkaXYgY2xhc3NOYW1lPVwibWluLWgtc2NyZWVuIGZsZXggaXRlbXMtY2VudGVyIGp1c3RpZnktY2VudGVyXCI+XHJcbiAgICAgICAgICAgIDxUZXN0UGFnZSAvPlxyXG4gICAgICAgIDwvZGl2PlxyXG4gICAgKTtcclxufVxyXG5cclxuY29uc3QgZWwgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnYXBwJyk7XHJcbmlmIChlbCkge1xyXG4gICAgY3JlYXRlUm9vdChlbCkucmVuZGVyKDxBcHAgLz4pO1xyXG59XHJcbiJdLCJmaWxlIjoiQzovVXNlcnMvcGVuYWovRG9jdW1lbnRzL2xhcmFiZWwrcmVhY3QvcmVzb3VyY2VzL2pzL2FwcC5qc3gifQ==
