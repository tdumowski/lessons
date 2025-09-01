import { ApolloClient, InMemoryCache, HttpLink, gql } from '@apollo/client';

// Inicjalizacja Apollo Client z HttpLink
const client = new ApolloClient({
  link: new HttpLink({
    uri: '/graphql', // Endpoint GraphQL w Twojej aplikacji Laravel
  }),
  cache: new InMemoryCache()
});

// Definicja zapytania GraphQL
const CHAT_QUERY = gql`
  query GetChatAnswer($input: chatAnswer!) {
    getChatAnswer(input: $input)
  }
`;

// Czekaj na załadowanie DOM
document.addEventListener('DOMContentLoaded', () => {
  const chatTrigger = document.getElementById('btnChatTrigger');
  
  // Sprawdź, czy element istnieje
  if (chatTrigger) {
    chatTrigger.addEventListener('click', async () => {
      // Pobranie wartości z inputa
      document.getElementById('chatAnswer').value = 'Czekam na odpowiedź...';
      const question = document.getElementById('chatQuestion').value;

      try {
        // Wysłanie zapytania GraphQL z input jako obiektem
        const { data } = await client.query({
          query: CHAT_QUERY,
          variables: { input: { prompt: question } }
        });

        // Wyświetlenie odpowiedzi w textarea
        const answer = data.getChatAnswer;
        document.getElementById('chatAnswer').value = answer;
      } catch (error) {
        console.error('Błąd podczas wysyłania zapytania GraphQL:', error);
        document.getElementById('chatAnswer').value = 'Wystąpił błąd podczas pobierania odpowiedzi.';
      }
    });
  }
});