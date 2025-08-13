FROM node:18-alpine

# Install system dependencies
RUN apk add --no-cache git

# Set working directory
WORKDIR /app

# Copy package files if they exist (using shell to handle missing files)
COPY package*.json* ./

# Install dependencies only if package.json exists
RUN if [ -f package.json ]; then npm install; else echo "No package.json found, skipping npm install"; fi

# Copy application code (will be mounted as volume)
# COPY . .

# Expose port 3000
EXPOSE 3000

# Default command (can be overridden)
CMD ["npm", "start"]
