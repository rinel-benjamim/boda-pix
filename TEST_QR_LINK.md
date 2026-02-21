# Teste de QR Code e Link Compartilhado

## Problemas Identificados e Corrigidos

### 1. QR Code Scanner
**Problema**: Código não estava sendo enviado corretamente no POST
**Solução**: 
- Extrair código da URL do QR
- Validar tamanho (8 caracteres)
- Converter para maiúsculas
- Usar setTimeout para garantir que o estado é atualizado antes do POST

### 2. Link Compartilhado
**Problema**: Link com ?code=XXX não processava automaticamente
**Solução**:
- Adicionar useEffect para detectar parâmetro 'code' na URL
- Auto-submit quando código válido é detectado
- Feedback visual com toast

### 3. Câmera Traseira
**Problema**: Scanner abria câmera frontal por padrão
**Solução**:
- Procurar por câmera com label contendo 'back', 'rear' ou 'environment'
- Fallback para última câmera da lista

## Como Testar

### Teste 1: QR Code
1. Criar evento
2. Gerar QR Code
3. Escanear com outro usuário
4. Verificar se entra no evento automaticamente

### Teste 2: Link Compartilhado
1. Copiar link de convite
2. Abrir em navegador (usuário diferente)
3. Verificar se entra automaticamente

### Teste 3: Código Manual
1. Ir para /events/join
2. Digitar código manualmente
3. Clicar em "Entrar no Evento"

## Código de Teste

```bash
# Criar usuário e evento
php artisan tinker

$user1 = User::factory()->create(['email' => 'user1@test.com']);
$user2 = User::factory()->create(['email' => 'user2@test.com']);

$event = Event::factory()->create([
    'created_by' => $user1->id,
    'name' => 'Teste QR',
    'access_code' => 'TESTQR01'
]);
$event->participants()->attach($user1->id, ['role' => 'admin']);

echo "Link: " . url('/events/join?code=TESTQR01') . "\n";
echo "Código: TESTQR01\n";
```

## Verificação

```bash
# Verificar se user2 entrou no evento
php artisan tinker

$event = Event::where('access_code', 'TESTQR01')->first();
$user2 = User::where('email', 'user2@test.com')->first();
echo $event->isMember($user2) ? "✓ Membro" : "✗ Não é membro";
```
