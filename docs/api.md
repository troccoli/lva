## Resources and Transformer

The _Laravel way_ of building API is to use a resource object. The framework provides a lot of
the functionality needed out of the box and it is extremely easy to use.

I use resources for almost all my models, apart from `Fixture`. For those I use a transformer.
I just wanted to show a different way of doing things. I would have preferred using this method
for another model, one that does not need pagination, e.g. `Season`, because the `ResourceCollection`
class provides it by default, while by using a transformer I will have to implement it myself.
