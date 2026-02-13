#!/bin/bash

echo "================================================"
echo "  OpsControl - Frontend Deployment"
echo "================================================"
echo ""

# Check if in frontend directory
if [ ! -f "frontend/package.json" ]; then
    echo "❌ Error: Run this script from the project root"
    exit 1
fi

# Navigate to frontend
cd frontend

echo "📦 Installing dependencies..."
npm install

echo ""
echo "🔨 Building for production..."
npm run build

echo ""
echo "================================================"
echo "  ✅ Build Complete!"
echo "================================================"
echo ""
echo "Your production build is ready in: frontend/dist/"
echo ""
echo "📤 Deploy Options:"
echo ""
echo "1. Drag & Drop:"
echo "   → Go to: https://app.netlify.com/drop"
echo "   → Drag the 'frontend/dist' folder"
echo ""
echo "2. Netlify CLI:"
echo "   → npm install -g netlify-cli"
echo "   → netlify login"
echo "   → netlify deploy --prod --dir=dist"
echo ""
echo "3. GitHub + Netlify:"
echo "   → Push to GitHub"
echo "   → Connect repo in Netlify"
echo ""
echo "📝 Don't forget to set environment variable in Netlify:"
echo "   VITE_USE_MOCK = true"
echo ""
echo "================================================"
