FROM nginx:alpine

# Remove default Nginx configurations
RUN rm -rf /etc/nginx/conf.d/*

# Copy our custom nginx configuration
COPY ./nginx/nginx.conf /etc/nginx/nginx.conf

# Expose port 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
