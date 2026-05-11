# ── Stage 1: Build ────────────────────────────────────────────────────────────
FROM node:24-alpine AS builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .

# Token wird per Docker Build Secret übergeben – landet nie als Layer im Image.
# Im Build immer draft, damit alle Inhalte geladen werden.
ENV STORYBLOK_VERSION=draft
ENV NODE_ENV=production

RUN --mount=type=secret,id=storyblok_token \
    STORYBLOK_DELIVERY_API_TOKEN=$(cat /run/secrets/storyblok_token) \
    npm run build

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
