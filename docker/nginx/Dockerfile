FROM nginx:alpine

# Copy custom nginx configuration from the nginx subfolder
COPY nginx/nginx.conf /etc/nginx/nginx.conf
COPY nginx/sites-available/default /etc/nginx/sites-available/default

# Create necessary directories and symlink
RUN mkdir -p /etc/nginx/sites-enabled && \
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Expose port 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
