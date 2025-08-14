FROM node:20-alpine

# Install system dependencies
RUN apk add --no-cache git

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json* ./

# Install dependencies
RUN npm install

# Copy application code (will be mounted as volume)
# COPY . .

# Expose port 3000 (standardized)
EXPOSE 3000

# Default command for development
CMD ["npm", "run", "dev"]
