<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado Histórico</title>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="iframe-embed">
    <div id="app"></div>
    <script type="module">
        import React from 'react';
        import { createRoot } from 'react-dom/client';
        import ListadoHistorico from './resources/js/components/ListadoHistorico.jsx';
        
        const container = document.getElementById('app');
        const root = createRoot(container);
        root.render(React.createElement(ListadoHistorico));
    </script>
</body>
</html>
