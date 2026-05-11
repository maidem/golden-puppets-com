# ── Stage 1: Build ────────────────────────────────────────────────────────────
FROM node:24-alpine AS builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .

# Token wird nur zur Build-Zeit benötigt und gelangt nicht in das finale Image.
# Coolify setzt den Token später als Runtime-Env-Variable.
ARG STORYBLOK_DELIVERY_API_TOKEN
ENV STORYBLOK_DELIVERY_API_TOKEN=$STORYBLOK_DELIVERY_API_TOKEN
# Im Build immer draft, damit alle Inhalte geladen werden
ENV STORYBLOK_VERSION=draft
ENV NODE_ENV=production

RUN npm run build

# ── Stage 2: Production Runner ────────────────────────────────────────────────
FROM node:24-alpine AS runner
WORKDIR /app

ENV NODE_ENV=production
ENV PORT=3000
# Default: draft für den Storyblok Visual Editor – kann per Coolify überschrieben werden
ENV STORYBLOK_VERSION=draft

COPY --from=builder /app/.output ./.output

EXPOSE 3000

CMD ["node", ".output/server/index.mjs"]
