export default defineEventHandler((event) => {
  setHeader(event, 'Content-Type', 'text/plain')

  // NOINDEX=true wird im Coolify-Preview-Container gesetzt
  if (process.env.NOINDEX === 'true') {
    return `# Preview / Visual Editor — keine Indexierung
User-agent: *
Disallow: /

User-agent: GPTBot
Disallow: /

User-agent: ChatGPT-User
Disallow: /

User-agent: OAI-SearchBot
Disallow: /

User-agent: anthropic-ai
Disallow: /

User-agent: ClaudeBot
Disallow: /

User-agent: Google-Extended
Disallow: /

User-agent: Gemini
Disallow: /

User-agent: PerplexityBot
Disallow: /

User-agent: CCBot
Disallow: /

User-agent: Bytespider
Disallow: /

User-agent: Applebot-Extended
Disallow: /

User-agent: cohere-ai
Disallow: /
`
  }

  // Produktion: alles erlaubt
  return `User-agent: *
Allow: /
`
})
