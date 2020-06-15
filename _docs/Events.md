### Documentation for the "Events" class.

*Allows the orchestration of "events" throughout a codebase by providing some simple methods to assign handlers to a particular event and to subsequently invoke those handlers at a later point in the codebase where the "event" is to occur.*

---


### Premise:

To understand the premise of this class, it should first be explained what is meant by an "event" in the context of this class. An "event", in the context of this class, is the point within a codebase where a particular, definable type of action should occur, but where the specific actions utilised in order to handle that particular, definable type of action mightn't necessarily be concrete, might need to be extensible, or entirely replaceable. A "handler", in the context of this class, refers to the specific actions assigned to an event.

The class is utilised by CIDRAM and phpMussel to provide a means by which users can, at their own discretion, entirely replace the default logging mechanisms provided by those packages, enrich them by extending those package's logging events with additional actions or handlers, or even potentially attach additional handlers to perform entirely unrelated actions onto those particular events at the packages in question if they so choose to do so.

---


### How to use:

- [addHandler method.](#addhandler-method)
- [addHandlerFinal method.](#addhandlerfinal-method)
- [destroyEvent method.](#destroyevent-method)
- [fireEvent method.](#fireevent-method)
- [assigned method.](#assigned-method)

#### addHandler method.

The addHandler method provides a way to add a handler to a method. It accepts three parameters.

```PHP
public function addHandler(string $Event, callable $Handler, bool $Replace = false): bool
```

The first parameter is the name of the event that the handler should be added to. The second parameter is the handler itself (an anonymous function or closure is generally the best approach to take). The third parameter is a boolean flag to indicate whether the handler should simply be appended onto the existing stack, or whether it should replace the stack entirely (specify true to replace, or false to append; because false is its default value, the parameter can also be omitted entirely when appending).

The addHandler method returns true when the handler is successfully added to the event, or false otherwise (e.g., if the event has previously been protected by addHandlerFinal).

The fireEvent method enables a handler to accept any number of optional parameters (the first parameter must be a string, and is passed by value; any subsequent parameters can be of any type, and are passed by reference). Handler aren't required to return anything, but may optionally return true/false to indicate whether execution was successful, which may help in facilitating any potential unit test requirements that the implementation might have.

#### addHandlerFinal method.

The addHandlerFinal method is identical to the addHandler method is every way, except that unlike the addHandler method, the addHandlerFinal method will protect the event against any subsequent handlers being added to its existing stack.

```PHP
public function addHandlerFinal(string $Event, callable $Handler, bool $Replace = false): bool
```

This can be useful when you need to ensure that a particular handler will be the final handler in the stack to be invoked. Note, however, that it only protects the existing stack, and that it therefore won't protect against new handlers being added by calls to addHandler or addHandlerFinal in cases where the new handler is to replace the existing stack entirely. It also won't protect against calls to destroyEvent.

#### destroyEvent method.

The destroyEvent method destroys the event and its entire handler stack from the object instance. It accepts one parameter: The name of the event to destroy. It returns true when the event is successfully destroyed, or false otherwise (e.g., if the event already doesn't exist).

```PHP
public function destroyEvent(string $Event): bool
```

#### fireEvent method.

The fireEvent method is used to iteratively execute all the handlers in the event's handler stack. It accepts one mandatory parameter, and may accept any number of optional parameters.

```PHP
public function fireEvent(string $Event, string $Data = '', &...$Misc): bool
```

The first parameter is the name of the event to fire. The second parameter is an optional string to supply to each handler in the stack when executed.

The fireEvent method returns true when the event has successfully fired (i.e., each handler in the stack has executed), or false otherwise (e.g., if called to fire a non-existent event).

#### assigned method.

The assigned method provides a way to check whether an event has had any handlers assigned to it. It accepts one parameter: The name of the event to check. It returns true when the event is known to the object instance (i.e., has handlers assigned to it), or false otherwise (i.e., doesn't exist or isn't known to the object instance).

```PHP
public function assigned(string $Event): bool
```

This can be useful in cases where data needs to be processed prior to calling fireEvent.

---


### A simple example:

```PHP
<?php
/** Instantiate the event orchestrator. */
$Events = new \Maikuolan\Common\Events();

/** Add a hypothetical handler to a hypothetical event. */
$Events->addHandler('aHypotheticalEvent', function ($Data): bool {

    /** Guard. */
    if (!is_writable(__DIR__ . 'foobar.txt')) {
        return false;
    }

    /** Open a file for writing and write the supplied data to it. */
    $File = fopen(__DIR__ . 'foobar.txt', 'wb');
    fwrite($File, $Data);
    fclose($File);
    return true;
});

/** Fire the hypothetical event. */
$Events->fireEvent('aHypotheticalEvent');
```

---


Last Updated: 15 June 2020 (2020.06.15).
