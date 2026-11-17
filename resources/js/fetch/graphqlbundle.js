import { print } from 'graphql'
import { gql } from 'graphql-tag'
import graphqlCombineQuery from 'graphql-combine-query'

const combineQuery = graphqlCombineQuery?.default ?? graphqlCombineQuery

export { print, combineQuery, gql }
