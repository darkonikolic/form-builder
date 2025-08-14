<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Builder API Documentation</title>
    
    <!-- Swagger UI CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('swagger-assets/swagger-ui.css') }}" />
    
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        
        *, *:before, *:after {
            box-sizing: inherit;
        }
        
        body {
            margin: 0;
            background: #fafafa;
        }
        
        .swagger-ui .topbar {
            background-color: #2c3e50;
        }
        
        .swagger-ui .topbar .download-url-wrapper .select-label {
            color: #fff;
        }
        
        .swagger-ui .topbar .download-url-wrapper input {
            border: 2px solid #34495e;
        }
        
        .swagger-ui .info .title {
            color: #2c3e50;
        }
        
        .swagger-ui .scheme-container {
            background-color: #ecf0f1;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    
    <!-- Swagger UI JavaScript -->
    <script src="{{ url('swagger-assets/swagger-ui-bundle.js') }}"></script>
    <script src="{{ url('swagger-assets/swagger-ui-standalone-preset.js') }}"></script>
    
    <script>
        window.onload = function() {
            // Swagger UI configuration
            const ui = SwaggerUIBundle({
                url: '/api/documentation.json',
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                docExpansion: 'list',
                filter: true,
                showExtensions: true,
                showCommonExtensions: true,
                tryItOutEnabled: true,
                requestInterceptor: function(request) {
                    // Add Bearer token if available
                    const token = localStorage.getItem('auth_token');
                    if (token) {
                        request.headers.Authorization = 'Bearer ' + token;
                    }
                    return request;
                },
                responseInterceptor: function(response) {
                    // Handle login response to save token
                    if (response.url && response.url.includes('/api/login') && response.status === 200) {
                        try {
                            const data = JSON.parse(response.body);
                            if (data.data && data.data.token) {
                                localStorage.setItem('auth_token', data.data.token);
                            }
                        } catch (e) {
                            console.log('Could not parse response');
                        }
                    }
                    return response;
                }
            });
            
            // Add custom CSS for better styling
            const style = document.createElement('style');
            style.textContent = `
                .swagger-ui .topbar { background-color: #2c3e50; }
                .swagger-ui .info .title { color: #2c3e50; }
                .swagger-ui .scheme-container { background-color: #ecf0f1; }
                .swagger-ui .opblock.opblock-post { background-color: rgba(73,204,144,0.1); }
                .swagger-ui .opblock.opblock-get { background-color: rgba(97,175,254,0.1); }
                .swagger-ui .opblock.opblock-put { background-color: rgba(252,161,48,0.1); }
                .swagger-ui .opblock.opblock-delete { background-color: rgba(249,62,62,0.1); }
            `;
            document.head.appendChild(style);
        };
    </script>
</body>
</html>
